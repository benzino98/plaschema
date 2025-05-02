# Technical Context: PLASCHEMA Website

## Technology Stack

### Backend

- **PHP 8.2** - Core language
- **Laravel 10.x** - PHP framework
- **MySQL 8.0** - Database management system
- **Artisan** - Laravel's command-line interface

### Frontend

- **Blade** - Laravel's templating engine
- **TailwindCSS 3.x** - Utility-first CSS framework
- **AlpineJS** - Lightweight JavaScript framework for interactivity
- **Livewire** - For dynamic components without building full SPA

### Development Tools

- **Composer** - PHP dependency management
- **npm/Node.js** - JavaScript dependency management
- **Laravel Mix** - Asset compilation
- **PHPUnit** - Testing framework
- **Laravel Debugbar** - Development debugging
- **Faker** - Test data generation

### Deployment & Infrastructure

- **Shared Hosting** - Production environment
- **Git** - Version control
- **Craft Digital Agency Server** - Hosting provider

## Development Environment

### Local Setup Requirements

- PHP 8.2+ with required extensions
- Composer 2.x
- Node.js 16+
- npm 8+
- MySQL 8.0+
- Git

### Installation Steps

1. Clone the repository
2. Run `composer install` to install PHP dependencies
3. Copy `.env.example` to `.env` and configure database settings
4. Run `php artisan key:generate` to generate application key
5. Run `php artisan migrate --seed` to set up database
6. Run `npm install` to install frontend dependencies
7. Run `npm run dev` to compile assets (development)
8. Run `php artisan serve` to start development server

## Key Dependencies

### PHP Packages

- **laravel/framework**: ^10.0 - Laravel core
- **laravel/sanctum**: ^3.2 - API token authentication
- **laravel/tinker**: ^2.7 - Interactive shell for Laravel
- **intervention/image**: ^2.7 - Image manipulation library
- **spatie/laravel-backup**: ^8.3 - Database and file backups
- **barryvdh/laravel-debugbar**: ^3.8 - Development debugging

### JavaScript Packages

- **alpinejs**: ^3.12 - JavaScript framework
- **tailwindcss**: ^3.3 - CSS framework
- **@tailwindcss/forms**: ^0.5 - Form styling
- **@tailwindcss/typography**: ^0.5 - Typography styling
- **autoprefixer**: ^10.4 - CSS processing
- **postcss**: ^8.4 - CSS transformer
- **laravel-mix**: ^6.0 - Asset compilation

## Database Structure

The database follows normalized design principles with the following key tables:

- **users** - Admin user accounts
- **healthcare_providers** - Healthcare provider details
- **news_articles** - News content
- **faqs** - Frequently asked questions
- **categories** - For organizing providers and FAQs
- **migrations** - Database version control
- **failed_jobs** - Tracking failed queue jobs
- **password_reset_tokens** - For password resets

## API & Integration Points

- Currently no external API integrations
- Potential future integration with regional healthcare systems

## Performance Considerations

- Image optimization using Intervention Image
- Lazy loading for non-critical images
- Asset minification in production
- Database query optimization
- Cache implementation for frequently accessed data

## Security Measures

- CSRF protection via Laravel middleware
- Form validation on all inputs
- Sanitized database queries via Eloquent
- Protected routes with authentication middleware
- Password hashing with bcrypt
- Rate limiting on login and form submissions

## Testing Infrastructure

- PHPUnit for feature and unit tests
- Factory-based test data generation
- Database transactions in tests
- Command for generating test users
- Browser testing with Laravel Dusk (planned)

## Deployment Pipeline

1. Code review and testing on development branch
2. Merge to main branch
3. Backup production database
4. Deploy code via Git pull on production
5. Run migrations
6. Clear caches
7. Verify deployment

## Monitoring & Maintenance

- Error logging to Laravel log files
- Regular database backups
- Monthly security updates
- Performance monitoring
