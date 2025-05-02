# Plaschema Website

This is a Laravel-based web application for Plaschema.

## Requirements

-   PHP 8.2+
-   Composer
-   Node.js & npm (for frontend assets)
-   A database (MySQL, PostgreSQL, SQLite, etc.)

## Installation

1. Clone the repository:

```bash
git clone <repository-url>
cd plaschema
```

2. Install PHP dependencies:

```bash
composer install
```

3. Create and configure environment file:

```bash
cp .env.example .env
php artisan key:generate
```

4. Configure your database connection in the `.env` file:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=plaschema
DB_USERNAME=root
DB_PASSWORD=
```

5. Run database migrations:

```bash
php artisan migrate
```

6. Install frontend dependencies and build assets (if needed):

```bash
npm install
npm run dev
```

## Running the Application

Start the development server:

```bash
php artisan serve
```

The application will be available at http://localhost:8000

## Project Structure

-   `app/` - Contains the core code of the application
-   `config/` - All of the application's configuration files
-   `database/` - Database migrations and seeders
-   `public/` - Publicly accessible files, entry point to the application
-   `resources/` - Views, raw assets, and language files
-   `routes/` - All route definitions
-   `storage/` - Application storage, logs, and compiled templates
-   `tests/` - Automated tests

## Development

This project follows Laravel's best practices. For more information, refer to the [Laravel documentation](https://laravel.com/docs).
