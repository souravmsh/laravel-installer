# Developer Guide

A practical guide for developing, testing, and contributing to `souravmsh/laravel-installer` locally within a Laravel host application.

---

## Table of Contents

- [Local Development Setup](#local-development-setup)
- [Running Tests](#running-tests)
- [Package Structure](#package-structure)
- [Making Changes](#making-changes)
- [Publishing Assets](#publishing-assets)
- [Resetting Installer State](#resetting-installer-state)
- [Coding Standards](#coding-standards)

---

## Local Development Setup

Instead of installing the package from Packagist, load it directly from your local filesystem using Composer's **path repository** feature. This creates a symlink so changes to the package source are reflected immediately — no `composer update` needed on every edit.

### 1. Place the package

Clone or copy the package into your host Laravel application:

```
your-laravel-app/
└── packages/
    └── souravmsh/
        └── laravel-installer/   ← this repository
```

### 2. Register the path repository

In your host application's **`composer.json`**, add a `repositories` block **before** any other keys:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "./packages/souravmsh/laravel-installer",
            "options": {
                "symlink": true
            }
        }
    ]
}
```

### 3. Set the version constraint to `@dev`

In the same `composer.json`, update the `require` entry:

```json
"require": {
    "souravmsh/laravel-installer": "@dev"
}
```

### 4. Install

```bash
composer update souravmsh/laravel-installer
```

Composer will symlink `vendor/souravmsh/laravel-installer` → `packages/souravmsh/laravel-installer`. Any file you edit in `packages/` is live instantly.

> **Reverting to Packagist**  
> Remove the `repositories` block and restore the version constraint (e.g. `"^1.2.5"`), then run `composer update souravmsh/laravel-installer`.

---

## Running Tests

The package uses **PHPUnit** via Orchestra Testbench.

### Install dev dependencies

```bash
cd packages/souravmsh/laravel-installer
composer install
```

### Run all tests

```bash
composer test
# or directly:
./vendor/bin/phpunit
```

### Run a specific test file

```bash
./vendor/bin/phpunit tests/InstallerTest.php
```

### Run a specific test method

```bash
./vendor/bin/phpunit --filter test_method_name
```

---

## Package Structure

```
laravel-installer/
├── config/
│   └── installer.php          # Published config file
├── public/                    # Published public assets (CSS, JS)
├── resources/
│   └── views/                 # Blade templates
├── src/
│   ├── Commands/              # Artisan commands
│   │   ├── InstallerFreshCommand.php
│   │   └── InstallerResetCommand.php
│   ├── Http/
│   │   ├── Controllers/       # Installer step controllers
│   │   └── Middleware/        # CheckInstalled middleware
│   ├── InstallerServiceProvider.php
│   └── ...
├── tests/
├── CHANGELOG.md
├── CONTRIBUTING.md
├── DEVELOPER.md               # ← you are here
├── LICENSE
└── README.md
```

---

## Making Changes

### Service Provider

`InstallerServiceProvider.php` is the entry point. It registers:
- Routes (`/install/*`)
- Middleware (`CheckInstalled`)
- Config, views, and public assets

### Artisan Commands

| Command | Description |
|---|---|
| `php artisan laravel-installer:install` | Publishes the package configuration file to the host app (also runs automatically during Composer install) |
| `php artisan laravel-installer:fresh` | Re-runs permission checks and setup |
| `php artisan laravel-installer:reset` | Removes the lock file to force setup mode |

### Middleware

`CheckInstalled` redirects unauthenticated traffic to `/install` if the lock file (`storage/installed`) is absent. It is **not active** on installer routes themselves.

### Lock File

The installer state is determined solely by the existence of:

```
storage/installed
```

Creating this file = installed. Deleting it = triggers the installer again.

---

## Publishing Assets

After making changes to config or views, re-publish them from the host app:

```bash
# Config
php artisan vendor:publish --tag=installer-config --force

# Views
php artisan vendor:publish --tag=installer-views --force

# Public assets
php artisan vendor:publish --tag=installer-assets --force

# Everything at once
php artisan vendor:publish --provider="Souravmsh\Installer\InstallerServiceProvider" --force
```

---

## Resetting Installer State

During development you'll frequently need to re-trigger the installer:

```bash
# Via Artisan (recommended)
php artisan laravel-installer:reset

# Manually
rm storage/installed
php artisan optimize:clear
```

---

## Coding Standards

- Follows **PSR-12** coding style.
- Format code before committing:

```bash
# From the host app root (if Laravel Pint is available)
./vendor/bin/pint packages/souravmsh/laravel-installer/src

# Or from inside the package
./vendor/bin/pint src/
```

- Keep service provider boot logic minimal — defer heavy work to controllers or commands.
- All new features should include a corresponding test in `tests/`.
