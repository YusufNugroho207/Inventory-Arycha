# Laravel Admin Panel with Filament

A Laravel 10 application using [Filament](https://filamentphp.com/) as an admin panel, enhanced with useful plugins like Filament Shield, Excel export, and profile editing. Built with Tailwind CSS and Vite for modern frontend development.

## ✨ Features

- Laravel 10 framework
- Filament v3 admin panel
- Role & permission management with Filament Shield
- Excel export for Filament tables
- Custom user profile editor
- Background customization support
- Sanctum for API authentication
- Tailwind CSS 3 + Typography & Forms plugins
- Modern frontend build with Vite

## 🧰 Tech Stack

| Layer     | Tools / Packages                                       |
|-----------|--------------------------------------------------------|
| Backend   | Laravel 10, Filament 3                                 |
| Auth      | Laravel Sanctum                                        |
| Styling   | Tailwind CSS, @tailwindcss/forms, @tailwindcss/typography |
| JS Tooling| Vite, Axios                                            |
| Dev Tools | Laravel Pint, PHPUnit, Collision, Faker, Sail         |

## 📦 Included Packages

### Required

- `filament/filament`: Core Filament package
- `bezhansalleh/filament-shield`: Role and permission management
- `pxlrbt/filament-excel`: Export Filament tables to Excel
- `joaopaulolndev/filament-edit-profile`: User profile management
- `swisnl/filament-backgrounds`: Change background image/style in Filament
- `laravel/sanctum`: API authentication
- `guzzlehttp/guzzle`: HTTP client

### Development

- `laravel/sail`: Local development environment
- `phpunit/phpunit`: Unit testing
- `laravel/pint`: Code formatting
- `fakerphp/faker`: Dummy data
- `nunomaduro/collision`: Error handling
- `vite`, `tailwindcss`, `postcss`, `axios`, etc.

## 🚀 Getting Started

### Prerequisites

- PHP 8.1+
- Composer
- Node.js & npm
- MySQL / PostgreSQL

### Installation

```bash
# Clone the repository
git clone https://github.com/your-username/your-project.git
cd your-project

# Install PHP dependencies
composer install

# Create .env file
cp .env.example .env

# Generate app key
php artisan key:generate

# Install JS dependencies
npm install

# Build frontend assets
npm run build

# Run migrations (if needed)
php artisan migrate

# Start local dev server
php artisan serve
