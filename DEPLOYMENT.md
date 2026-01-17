# MeetingMan Deployment Guide

## Pre-Deployment Checklist

### Server Requirements
- [ ] PHP 8.2+
- [ ] Composer
- [ ] MySQL/MariaDB or PostgreSQL
- [ ] Node.js & npm (for asset building) - *or build locally*
- [ ] SSL certificate (usually included with hosting)

### Domain & DNS
- [ ] Domain name configured
- [ ] DNS pointing to server IP

---

## Shared Hosting (20i / cPanel / Similar)

### First-Time Setup

1. **Use the one-click Laravel installer** to create the app
2. **Set up your database** via the hosting panel
3. **SSH into your server:**
   ```bash
   ssh username@yourserver.com
   cd ~/public_html  # or wherever your Laravel app is
   ```

4. **Replace the default Laravel files with your code:**
   ```bash
   # Remove default Laravel files (keep .env if already configured)
   # Then clone your repo or upload via SFTP
   git clone https://github.com/yourusername/meetingman.git .
   ```

5. **Configure `.env`** with your database credentials and settings

6. **Run the deploy script:**
   ```bash
   chmod +x deploy.sh
   ./deploy.sh
   ```

### Subsequent Deployments

Just SSH in and run:
```bash
cd ~/public_html
git pull origin main
./deploy.sh
```

### If Node.js is NOT Available

Build assets locally before pushing:
```bash
# On your local machine
npm run build
git add public/build
git commit -m "Build assets for production"
git push
```

Then on the server, the deploy script will skip the npm step.

---

## VPS / Dedicated Server Deployment

### 1. Server Setup
```bash
# Clone repository
git clone <repo-url> /var/www/meetingman
cd /var/www/meetingman

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install && npm run build

# Set permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### 2. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with production values:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=meetingman
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password

SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=strict

# Mail Configuration (for meeting summary emails)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org   # or your provider
MAIL_PORT=587
MAIL_USERNAME=your_smtp_user
MAIL_PASSWORD=your_smtp_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="meetings@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Mail Provider Options
- **Mailgun** - Good free tier, easy setup
- **Postmark** - Great deliverability, transactional focus
- **Amazon SES** - Very cheap at scale
- **SMTP2GO** - Simple and reliable

### 3. Database Setup
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE meetingman;"

# Run migrations
php artisan migrate --force
```

### 4. Encrypt Existing Data (if migrating from existing install)
```bash
php artisan security:encrypt-existing-data
```

### 5. SSL/HTTPS Setup (Let's Encrypt)
```bash
# Install certbot
sudo apt install certbot python3-certbot-nginx

# Obtain certificate
sudo certbot --nginx -d yourdomain.com

# Auto-renewal is configured automatically
```

### 6. Web Server Configuration

#### Nginx Example
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com;
    root /var/www/meetingman/public;

    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 7. Optimize for Production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan icons:cache  # if using blade-icons
```

### 8. Set Up Scheduler (for future scheduled tasks)
Add to crontab (`crontab -e`):
```
* * * * * cd /var/www/meetingman && php artisan schedule:run >> /dev/null 2>&1
```

---

## Security Checklist

- [ ] `APP_DEBUG=false` in production
- [ ] `SESSION_SECURE_COOKIE=true` (requires HTTPS)
- [ ] Strong database password
- [ ] Firewall configured (only ports 80, 443, 22)
- [ ] Database not exposed to internet (localhost only)
- [ ] Regular backups configured
- [ ] File permissions correct (storage, bootstrap/cache writable)

---

## Backup Strategy

### Database Backup
```bash
# Manual backup
mysqldump -u meetingman_user -p meetingman > backup_$(date +%Y%m%d).sql

# Restore
mysql -u meetingman_user -p meetingman < backup_file.sql
```

### Recommended: Automated Daily Backups
Set up a cron job or use a service like:
- Laravel Backup package (`spatie/laravel-backup`)
- Server-level backup (DigitalOcean/AWS snapshots)

---

## Post-Deployment Verification

- [ ] Site loads over HTTPS
- [ ] Can register new account (password rules enforced)
- [ ] Can log in
- [ ] Can enable 2FA in Settings
- [ ] Can create people, meetings, actions, objectives
- [ ] Meeting summary email sends correctly (check spam folder)
- [ ] Session expires after closing browser
- [ ] HTTP redirects to HTTPS

---

## Troubleshooting

### 500 Error
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Check permissions
chown -R www-data:www-data storage bootstrap/cache
```

### Assets Not Loading
```bash
npm run build
php artisan view:clear
```

### Session Issues
```bash
php artisan session:table  # if using database sessions
php artisan migrate
```

### Email Not Sending
```bash
# Test mail configuration
php artisan tinker
>>> Mail::raw('Test email', fn($m) => $m->to('your@email.com'));

# Check logs for mail errors
tail -f storage/logs/laravel.log | grep -i mail
```

Common issues:
- Wrong SMTP credentials
- Port blocked by firewall (try 587 or 465)
- Missing `MAIL_FROM_ADDRESS` in .env
