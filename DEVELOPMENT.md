# MeetingMan Development Notes

## Project Overview
A Laravel 12 application for tracking 1:1 meetings, actions, and objectives with team members.

## Tech Stack
- Laravel 12 with Breeze (Blade)
- Tailwind CSS
- MySQL database
- Laravel Fortify for 2FA

## Brand
- Primary purple: #8838e0
- Secondary blue: #355afe
- Gradient: `linear-gradient(135deg, #8838e0 0%, #355afe 100%)`
- Font: Inter (via Bunny Fonts)

---

## Completed Features

### Core Functionality
- **People management** - CRUD, archive/restore, meeting frequency setting
- **Meetings** - Record with date, mood (emoji), topics, action items
- **Actions** - Track status (not started, on track, complete, dropped), due dates, assignments
- **Objectives** - Track per person with status (not started, on track, off track, complete, dropped)

### Dashboard
- Summary stats (total people, overdue actions, due soon, off-track objectives)
- Stats link to filtered global views
- Top 5 people cards with "needs attention" badges
- Smart sorting (needs attention first, then alphabetical)

### Global Views
- `/actions?filter=overdue|due_soon|pending` - Filtered action list grouped by person
- `/objectives?filter=off_track|active` - Filtered objectives list

### Meeting Features
- Meeting frequency per person (days)
- "Meeting overdue" detection and display
- Email summary - preview in browser, optional send on meeting create
- Mood tracking with emoji display

### Settings
- Default meeting frequency
- Two-factor authentication (enable/disable, QR code, recovery codes)

### Security (Implemented)
- Field-level encryption for sensitive data (topics, action descriptions, objective definitions)
- Strong password policy (12+ chars, mixed case, numbers, symbols, breach check)
- Session hardening (60min timeout, expire on close, encrypted, strict same-site)
- Rate limiting on auth routes (5 attempts/min login, 3 attempts/min password reset)
- 2FA via Laravel Fortify with TOTP

### Admin Panel
- User management (suspend, promote/demote, force password reset)
- Company management
- Audit logs for admin actions
- Branding settings (custom logo, site name)
- **Auto-updater** - Check and install updates from GitHub without SSH

### UI/UX
- Marketing-style login page with feature highlights
- Footer with site name and version number
- Responsive design throughout

### Email
- `MeetingSummary` Mailable class
- Markdown email template at `resources/views/emails/meeting-summary.blade.php`
- Preview route at `/meetings/{id}/email-preview`
- Optional checkbox on meeting create form to send email
- Mail configured for `log` driver in development

---

## Deployment Setup

### Hosting
- Target: 20i shared hosting (SSH access, no Node.js)
- Assets built locally and committed to git

### Files
- `deploy.sh` - Deployment script (composer, migrations, cache)
- `DEPLOYMENT.md` - Full deployment guide with shared hosting instructions

### Deployment Workflow
1. Local: `npm run build && git add -A && git commit && git push`
2. Server: `git pull && ./deploy.sh`

### Pre-Deploy Checklist
- [ ] Set up Git repository
- [ ] Configure production `.env` on server
- [ ] Set up database via hosting panel
- [ ] Configure mail provider (Mailgun, Postmark, etc.)
- [ ] Run `php artisan security:encrypt-existing-data` if migrating data

---

## Releasing Updates

MeetingMan includes a built-in auto-updater that allows admins to update from the Admin panel without SSH access.

### Release Workflow

1. **Make your changes** and test locally

2. **Update the VERSION file** in the project root:
   ```bash
   echo "1.0.4" > VERSION
   ```

3. **Commit and push**:
   ```bash
   git add -A
   git commit -m "Description of changes

   Co-Authored-By: Claude Opus 4.5 <noreply@anthropic.com>"
   git push
   ```

4. **Create a GitHub release** using the `gh` CLI:
   ```bash
   gh release create v1.0.4 --title "v1.0.4" --notes "## What's New

   - Feature or fix description here"
   ```

### How the Auto-Updater Works

- Located at **Admin > Updates** (super admin only)
- Checks GitHub API for latest release: `https://api.github.com/repos/cinsekrap/MeetingMan/releases/latest`
- Downloads release zip from GitHub
- Extracts and copies files while preserving protected paths
- Clears all Laravel caches after update

### Protected Paths (never overwritten)

These files/directories are preserved during updates:
- `.env` - Environment configuration
- `storage/installed` - Installation marker
- `storage/logs/*` - Log files
- `storage/app/*` - Uploaded files (logos, etc.)
- `storage/framework/sessions/*` - User sessions
- `storage/framework/cache/*` - Cache data
- `storage/framework/views/*` - Compiled views
- `bootstrap/cache/*` - Compiled app cache

### Key Files for Auto-Updater

- `VERSION` - Current version number (e.g., "1.0.3")
- `app/Services/UpdateService.php` - Core update logic
- `app/Http/Controllers/Admin/AdminUpdateController.php` - Admin UI controller
- `resources/views/admin/updates/index.blade.php` - Update UI

### Important Notes

- Version numbers must follow semver (e.g., 1.0.0, 1.0.1, 1.2.0)
- GitHub release tags must be prefixed with `v` (e.g., v1.0.3)
- The layout requires `@stack('scripts')` before `</body>` for JS to work
- Update actions are logged via AdminAuditService

---

## Pending / Future Features

- Scheduled email reminders (overdue actions, meeting due)
- Weekly digest emails
- More email triggers (action assigned, objective off-track)

---

## Key Files Reference

### Models
- `app/Models/Person.php` - includes `isMeetingOverdue()` method
- `app/Models/Meeting.php` - has `mood_emoji` accessor
- `app/Models/Action.php` - encrypted `description`, status scopes
- `app/Models/Objective.php` - encrypted `definition`
- `app/Models/MeetingTopic.php` - encrypted `content`
- `app/Models/UserSetting.php` - user preferences

### Controllers
- `app/Http/Controllers/DashboardController.php` - complex stats query
- `app/Http/Controllers/MeetingController.php` - includes email sending logic
- `app/Http/Controllers/SettingsController.php` - settings + 2FA UI

### Views
- `resources/views/dashboard.blade.php` - main dashboard
- `resources/views/settings/index.blade.php` - settings with 2FA management
- `resources/views/emails/meeting-summary.blade.php` - email template

### Config
- `config/session.php` - hardened session settings
- `config/fortify.php` - 2FA configuration

### Commands
- `php artisan security:encrypt-existing-data` - Encrypt existing unencrypted data

---

## Local Development

### Requirements
- PHP 8.2+
- Composer
- Node.js & npm (via Herd's nvm)
- MySQL

### Running Locally
```bash
# Start server (using Laravel Herd)
# Site available at meetingman.test

# Build assets
npm run dev   # Development with hot reload
npm run build # Production build

# Run migrations
php artisan migrate
```

### Mail Testing
Currently set to `MAIL_MAILER=log` - emails appear in `storage/logs/laravel.log`

---

*Last updated: 17 January 2026*
