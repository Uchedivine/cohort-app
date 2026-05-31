# Cohort Web App

> A comprehensive web platform for managing and showcasing a cohort of African civil society organizations driving systems change across health, environment, digital equity, food security, and community resilience.

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

---

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [User Roles](#user-roles)
- [Testing](#testing)
- [Documentation](#documentation)
- [Contributing](#contributing)
- [License](#license)

---

## 🌟 Overview

The Cohort Web App is a production-ready Laravel application designed to amplify the impact of African civil society organizations. It provides:

- **Public Directory** - Showcase member organizations with detailed profiles
- **Story Bank** - Share impact stories and success narratives
- **Resources Library** - Centralized repository for tools, guides, and research
- **Events Calendar** - Manage and promote cohort events and activities
- **Member Portal** - Secure submission and approval workflow system

**Built for**: Thoughts and Mace Advisory  
**Target Users**: 15+ organizations across 7 African countries  
**Impact**: 2M+ lives reached, 12 SDGs addressed

---

## ✨ Features

### Public Features
- 🏢 **Organization Directory** - Browse and search member organizations
- 📖 **Story Bank** - Read inspiring impact stories with rich media
- 📚 **Resources Library** - Access tools, guides, and research materials
- 📅 **Events Calendar** - View upcoming events in list or calendar format
- 🔍 **Global Search** - Search across all content types with filters
- 📱 **Mobile Responsive** - Optimized for all devices

### Member Portal
- ✍️ **Content Submission** - Submit stories, resources, and updates
- 📊 **Dashboard** - Track submission status and activity
- 🔄 **Revision System** - Resubmit content after feedback
- 📧 **Email Notifications** - Stay updated on submission status

### Admin Features (Secretary)
- ✅ **Approval Workflow** - Review, approve, reject, or request changes
- 👥 **User Management** - Create and manage organization accounts
- 🏷️ **Tag Management** - Organize content with custom tags
- 📅 **Event Management** - Create and publish events with media
- 📈 **Analytics Dashboard** - Monitor submissions and activity
- 🔍 **Content Moderation** - Before/after comparison for edits

---

## 🛠 Tech Stack

### Backend
- **Framework**: Laravel 11.x
- **PHP**: 8.2+
- **Database**: MySQL 8.0+ / PostgreSQL 13+
- **Authentication**: Laravel Breeze
- **Search**: Laravel Scout (Database Driver)
- **Queue**: Redis / Database
- **Mail**: SMTP / Mailgun / SendGrid

### Frontend
- **Templating**: Blade
- **CSS**: Custom CSS with CSS Variables
- **JavaScript**: Vanilla JS + FullCalendar.js
- **Fonts**: Cormorant Garamond, DM Sans
- **Icons**: Emoji-based (no icon library needed)

### Development
- **Testing**: PHPUnit (44 tests, 70%+ coverage)
- **Code Style**: PSR-12
- **Version Control**: Git
- **Package Manager**: Composer

---

## 📦 Requirements

- PHP >= 8.2
- Composer
- MySQL 8.0+ or PostgreSQL 13+
- Node.js & NPM (for asset compilation)
- Redis (optional, for queue/cache)

---

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/Uchedivine/cohort-app.git
cd cohort-app
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 3. Environment Setup
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure Database
Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cohort_app
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Run Migrations & Seeders
```bash
# Run migrations
php artisan migrate

# Seed database with roles and default secretary account
php artisan db:seed
```

### 6. Link Storage
```bash
php artisan storage:link
```

### 7. Build Assets
```bash
npm run build
```

### 8. Start Development Server
```bash
php artisan serve
```

Visit: `http://localhost:8000`

---

## ⚙️ Configuration

### Mail Configuration
Configure mail settings in `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@cohortapp.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Queue Configuration
For background jobs (image optimization, emails):
```env
QUEUE_CONNECTION=database
```

Run queue worker:
```bash
php artisan queue:work
```

### Search Configuration
Laravel Scout is configured with database driver by default. No additional setup needed.

### File Upload Limits
Configure in `config/filesystems.php` or `.env`:
```env
UPLOAD_MAX_SIZE=10240  # 10MB in KB
```

---

## 👥 User Roles

### 1. Public Visitor
- View published content (no login required)
- Search and filter organizations, stories, resources, events
- Access: All public pages

### 2. Organization Editor (`org_editor`)
- Edit own organization profile
- Submit stories and resources
- View submission status
- Cannot publish content
- Access: `/org-editor/*`

### 3. Secretary (`secretary`)
- Review and approve all submissions
- Publish content
- Manage users and organizations
- Create and manage events
- Full administrative access
- Access: `/secretary/*`

---

## 🧪 Testing

### Run All Tests
```bash
php artisan test
```

### Run Specific Test Suite
```bash
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit
```

### Run with Coverage
```bash
php artisan test --coverage
```

### Test Statistics
- **Total Tests**: 44
- **Coverage**: 70%+
- **Test Suites**: Feature, Unit
- **Test Types**: HTTP, Database, Authentication, Authorization

---

## 📚 Documentation

### Project Documentation
- [`REQUIREMENTS_COMPLIANCE_REPORT.md`](REQUIREMENTS_COMPLIANCE_REPORT.md) - Full requirements analysis
- [`FEATURES_IMPLEMENTED.md`](FEATURES_IMPLEMENTED.md) - Feature implementation details
- [`HOVER_ANIMATION_IMPLEMENTATION.md`](HOVER_ANIMATION_IMPLEMENTATION.md) - UI animation guide
- [`PROJECT_STATUS_SUMMARY.md`](PROJECT_STATUS_SUMMARY.md) - Project status overview

### Architecture
- **MVC Pattern**: Clean separation of concerns
- **Service Layer**: Business logic in dedicated services
- **Form Requests**: Validation separated from controllers
- **Events & Listeners**: Decoupled notification system
- **Jobs & Queues**: Asynchronous processing

### Key Directories
```
app/
├── Http/Controllers/     # Request handling
│   ├── Public/          # Public-facing controllers
│   ├── OrgEditor/       # Organization editor controllers
│   └── Secretary/       # Admin controllers
├── Models/              # Eloquent models
├── Services/            # Business logic
├── Jobs/                # Queue jobs
├── Mail/                # Email templates
└── Events/              # Event classes

resources/
├── views/               # Blade templates
│   ├── public/         # Public pages
│   ├── org-editor/     # Member portal
│   └── secretary/      # Admin panel
└── css/                # Stylesheets

database/
├── migrations/         # Database schema
└── seeders/           # Database seeders

tests/
├── Feature/           # Feature tests
└── Unit/             # Unit tests
```

---

## 🎨 Design System

### Color Palette
```css
--navy:   #0f172a  /* Primary brand color */
--gold:   #d4af37  /* Accent color */
--green:  #059669  /* Secondary color */
--cream:  #f5f1e8  /* Background color */
```

### Typography
- **Headings**: Cormorant Garamond (serif)
- **Body**: DM Sans (sans-serif)

### Components
- Cards with navy blue hover animation (8px lift)
- Responsive grid layouts
- Mobile-first design
- Accessible forms and navigation

---

## 🔒 Security

### Implemented Security Features
- ✅ Strong password policy (8+ chars, mixed case, numbers, special chars)
- ✅ CSRF protection on all forms
- ✅ SQL injection prevention (Eloquent ORM)
- ✅ XSS protection (Blade escaping)
- ✅ File upload validation (type, size, MIME)
- ✅ Role-based access control (middleware)
- ✅ Email verification
- ✅ Password reset functionality

### Security Best Practices
- Never commit `.env` file
- Change default secretary password
- Use HTTPS in production
- Enable rate limiting
- Regular security updates
- Database backups

---

## 🚀 Deployment

### Production Checklist
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Configure production database
- [ ] Set up mail service (Mailgun, SendGrid, etc.)
- [ ] Configure queue worker (Supervisor)
- [ ] Set up cron for scheduled tasks
- [ ] Enable HTTPS/SSL
- [ ] Configure file storage (S3, DigitalOcean Spaces)
- [ ] Set up database backups
- [ ] Change default secretary password
- [ ] Run `php artisan optimize`

### Deployment Commands
```bash
# Pull latest code
git pull origin main

# Install dependencies
composer install --optimize-autoloader --no-dev

# Run migrations
php artisan migrate --force

# Clear and cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart queue workers
php artisan queue:restart
```

### Scheduled Tasks
Add to crontab:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🤝 Contributing

### Development Workflow
1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Code Standards
- Follow PSR-12 coding standards
- Write tests for new features
- Update documentation
- Use meaningful commit messages

### Running Code Quality Tools
```bash
# PHP Code Sniffer
./vendor/bin/phpcs

# PHP Stan
./vendor/bin/phpstan analyse

# PHP Unit
php artisan test
```

---

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 👨‍💻 Development Team

**Built by**: Development Team  
**Client**: Thoughts and Mace Advisory  
**Project Type**: Civil Society Cohort Management Platform  
**Status**: Production Ready ✅

---

## 📞 Support

For support, email support@cohortapp.com or open an issue on GitHub.

---

## 🙏 Acknowledgments

- Laravel Framework
- FullCalendar.js for calendar functionality
- All contributing organizations in the cohort
- Thoughts and Mace Advisory for project vision

---

## 📊 Project Statistics

- **Lines of Code**: ~12,000
- **Files**: 150+
- **Tests**: 44 (70%+ coverage)
- **User Roles**: 3
- **Content Types**: 4 (Organizations, Stories, Resources, Events)
- **Submission Statuses**: 6 (Draft, Submitted, Needs Changes, Approved, Published, Rejected)

---

**Made with ❤️ for African Civil Societys**
