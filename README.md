[![Software License](https://img.shields.io/badge/license-Proprietary-red.svg?style=flat-square)](LICENSE)

A sleek, modern, and easy-to-use web-based installer for Laravel applications. This package provides a step-by-step wizard to help users set up your Laravel application, including environment configuration, database connection, and license validation.

## Features

- **Standard Requirements Check**: Verifies PHP version and required extensions.
- **Permissions Check**: Ensures necessary directories are writable.
- **Environment Configuration**: Easy setup for `.env` variables.
- **Database Setup**: Wizard for database connection and migration.
- **License Validation**: Built-in support for license key validation.
- **Modern UI**: Clean and responsive design.

## Installation

You can install the package via composer:

```bash
composer require souravmsh/laravel-installer
```

## Setup

### 1. Register the Service Provider (Optional for Laravel 5.5+)

The package will automatically register itself using Laravel's package discovery.

### 2. Publish Configuration and Assets

Publish the configuration file and views:

```bash
php artisan vendor:publish --tag="laravel-installer-config"
php artisan vendor:publish --tag="installer-views"
```

### 3. Middleware

The package provides two middlewares:
- `installer.check`: Ensures the application is installed before allowing access.
- `installer.redirect`: Redirects to the homepage if the application is already installed.

You should apply these to your routes as needed.

## Configuration

The configuration file is located at `config/laravel_installer.php`. You can customize the application name, license server, and other settings here.

```php
return [
    'app_name' => env('APP_NAME', 'Laravel Application'),
    'license_check' => true,
    'license_server_url' => 'https://codekernel.net/api/v1/license',
];
```

## Usage

Once installed, navigate to `/install` to begin the installation process. The installer will guide you through:

1.  Welcome Screen
2.  Server Requirements Check
3.  Directory Permissions Check
4.  Environment Configuration
5.  Database Setup
6.  License Validation
7.  Installation Completion

## Security

If you discover any security-related issues, please email sourav.diubd@gmail.com instead of using the issue tracker.

## Credits

- [Shohrab Hossain](https://github.com/souravmsh)
- [All Contributors](../../contributors)

## License

Proprietary License. Please see [License File](LICENSE) for more information.
