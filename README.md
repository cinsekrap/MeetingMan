# MeetingMan

A one-on-one meeting management application for managers. Track your direct reports, record meetings, manage action items, and set objectives.

## Features

- **People Management** - Track your direct reports with support for organisational hierarchy
- **Meeting Records** - Document one-on-one meetings with structured topics (wins, challenges, actions, etc.)
- **Action Tracking** - Create and track action items from meetings with due dates and status
- **Objectives** - Set and monitor objectives for your team members
- **Multi-Company Support** - Manage people across multiple companies/teams
- **Admin Panel** - Super admin functionality for user management and audit logging
- **Mobile Responsive** - Works on desktop and mobile devices

## Requirements

- PHP 8.2 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Composer
- Node.js & NPM

### Required PHP Extensions

- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- PDO MySQL
- Tokenizer
- XML

## Installation

### Option 1: Web Installer (No SSH Required)

1. Download or clone the repository to your web server
2. Run `composer install --no-dev --optimize-autoloader`
3. Run `npm install && npm run build`
4. Copy `.env.example` to `.env`
5. Point your web server to the `public` directory
6. Visit your site in a browser - you'll be redirected to the installer
7. Follow the installation wizard to configure your database and create an admin account

### Option 2: Command Line

1. Clone the repository:
   ```bash
   git clone https://github.com/cinsekrap/MeetingMan.git
   cd MeetingMan
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install and build frontend assets:
   ```bash
   npm install
   npm run build
   ```

4. Configure environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. Edit `.env` with your database credentials:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=meetingman
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. Run migrations:
   ```bash
   php artisan migrate
   ```

7. Create storage symlink:
   ```bash
   php artisan storage:link
   ```

8. Create your first user by visiting the site and registering, then promote to super admin:
   ```bash
   php artisan tinker
   >>> User::first()->update(['is_super_admin' => true]);
   ```

## Usage

1. **Register/Login** - Create an account or log in
2. **Create a Company** - Set up your first company/team
3. **Add People** - Add your direct reports
4. **Record Meetings** - Document your one-on-ones
5. **Track Actions** - Manage action items and follow-ups
6. **Set Objectives** - Create and monitor team objectives

## Configuration

Key environment variables:

| Variable | Description | Default |
|----------|-------------|---------|
| `APP_NAME` | Application name | MeetingMan |
| `APP_ENV` | Environment (local/production) | production |
| `APP_DEBUG` | Enable debug mode | false |
| `APP_URL` | Your application URL | http://localhost |
| `DB_*` | Database configuration | MySQL |
| `MAIL_*` | Email configuration | SMTP |

## Development

For local development:

```bash
# Install dependencies
composer install
npm install

# Run development server
npm run dev

# In another terminal
php artisan serve
```

## License

This project is open-sourced software.

## Built With

- [Laravel](https://laravel.com) - PHP Framework
- [Tailwind CSS](https://tailwindcss.com) - CSS Framework
- [Alpine.js](https://alpinejs.dev) - JavaScript Framework
