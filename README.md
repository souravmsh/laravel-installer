[![Software License](https://img.shields.io/badge/license-Proprietary-red.svg?style=flat-square)](LICENSE)

A sleek, modern, and easy-to-use web-based installer for Laravel applications. This package provides a step-by-step wizard to help users set up your Laravel application, including environment configuration, database connection, and license validation.

## Features

- **Standard Requirements Check**: Verifies PHP version and required extensions.
- **Permissions Check**: Ensures necessary directories are writable.
- **Environment Configuration**: Easy setup for `.env` variables.
- **Database Setup**: Wizard for database connection and migration.
- **License Validation**: Flexible support for license key validation (Required, Optional, or Disabled).
- **Modern Mobile-App UI**: Sleek, glassmorphism-based design optimized for clarity and ease of use.

## Installation

You can install the package via composer:

```bash
composer require souravmsh/laravel-installer
```

## Setup

### 1. Register the Service Provider (Optional for Laravel 5.5+)

The package will automatically register itself using Laravel's package discovery.

### 2. Publish Configuration and Assets

You can publish the configuration file and views using the following commands:

**Publish All:**

```bash
php artisan vendor:publish --tag="laravel-installer-publish"
```

**Publish Configuration Only:**

```bash
php artisan vendor:publish --tag="laravel-installer-config"
```

**Publish Views Only:**

```bash
php artisan vendor:publish --tag="laravel-installer-views"
```

### 3. Middleware

The package provides two middlewares:

- `installer.check`: Ensures the application is installed before allowing access.
- `installer.redirect`: Redirects to the homepage if the application is already installed.

You should apply these to your routes as needed.

The configuration file is located at `config/laravel_installer.php`. You can customize the application name, license server, and other settings here.

```php
return [
    'app_name' => env('APP_NAME', 'Laravel Application'),

    // Enable or disable the installer
    'installer_enabled' => env('INSTALLER_ENABLED', true),

    // Path to the installation lock file
    'installed_key_path' => env('INSTALLER_KEY_PATH', 'app/private/key.install'),

    // Supported: "required", "optional", "disabled"
    'license_check' => env('INSTALLER_LICENSE', 'optional'),

    'license_server_url' => env('INSTALLER_LICENSE_URL', 'null'),
    'license_storage_path' => env('INSTALLER_LICENSE_KEY_PATH', 'app/private/key.private'),
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

### Commands

#### Reset / Fresh Install

Use this command to tear down the current installation and return the application to setup mode. It is safe to run at any time — it includes a confirmation prompt to prevent accidental resets.

```bash
php artisan laravel-installer:reset
```

The command runs three sequential steps:

| Step | Action |
|------|--------|
| **1. Fix permissions** | Sets write permissions on `.env` (664), `storage/` (775 recursive), `bootstrap/cache/` (775), and `storage/app/private/` (775). Creates missing directories automatically. |
| **2. Remove lock files** | Deletes the install lock file (`key.install`) **and** the license key file (`key.private`) if they exist. Prints the resolved path of each so you can verify. |
| **3. Clear all caches** | Runs `optimize:clear` which flushes config, route, view, event, and compiled bootstrap caches — including `bootstrap/cache/config.php` that individual `cache:clear` calls miss. |

After the command completes, the `installer.check` middleware will detect the missing lock file and redirect all protected routes to the setup wizard.

> **Note:** If `INSTALLER_ENABLED=false` is set in your `.env`, the middleware bypasses the lock-file check entirely. The command will warn you if this is the case — set `INSTALLER_ENABLED=true` to re-enable setup mode.

## Security

If you discover any security-related issues, please email sourav.diubd@gmail.com instead of using the issue tracker.

## Credits

- [Shohrab Hossain](https://github.com/souravmsh)
- [All Contributors](../../contributors)

## License

Proprietary License. Please see [License File](LICENSE) for more information.
