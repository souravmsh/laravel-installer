# Changelog

All notable changes to `souravmsh/laravel-installer` will be documented in this file.

## [1.2.5] - 2026-06-29

### Fixed
- **`CheckInstalled` middleware** — no longer force-redirects to the welcome page when the request is already targeting an installer route (`installer.*`). Previously this caused an infinite redirect loop and broke inter-step navigation.
- **`fixPermissions` command** — PHP's `chmod()` silently fails when the process doesn't own the target file (e.g. files owned by root or a deploy user). Added a shell `exec("chmod ...")` fallback so permissions are actually applied. Failure message now prints the exact `sudo chmod` command to run manually.
- **`.env` writable check** — replaced unreliable `app()->environmentFilePath()` with `base_path('.env')`. Falls back to checking the project root directory when `.env` doesn't yet exist (instead of calling `dirname()` on a cached path).
- **`bootstrap/cache` writable check** — added as a new permission requirement alongside `storage/` and `.env`.

### Added
- **Permissions section on welcome page** — the System Requirements checklist is now split into two labelled card sections:
  - **System Requirements** — PHP version and required extensions.
  - **File & Directory Permissions** — `storage/`, `bootstrap/cache/`, and `.env`, each showing the actual filesystem path and a `Writable` / `Not Writable` badge.

## [1.2.4] - 2026-06-29

### Changed
- **`laravel-installer:reset` command overhauled** — now runs three sequential steps:
  1. **Fix permissions**: `chmod 664` on `.env`; `chmod 775` (recursive) on `storage/`, `bootstrap/cache/`, and `storage/app/private/`. Missing directories are created automatically. Reports per-path success/failure so ownership issues are immediately visible.
  2. **Remove lock files**: Resolves lock-file paths from config *before* clearing caches (prevents stale in-memory config from targeting the wrong path). Removes both `key.install` and `key.private` if present. Prints resolved paths for transparency.
  3. **Clear all caches**: Replaced individual `cache:clear` / `config:clear` / `route:clear` / `view:clear` calls with a single `optimize:clear`, which also flushes the compiled bootstrap cache (`bootstrap/cache/config.php`) — the root cause of the previous "not moving to setup mode" bug.
- Added a final warning when `INSTALLER_ENABLED=false` is detected in `.env`, since removing the lock file alone cannot restore setup mode in that state.

## [1.2.3] - 2026-06-29

### Added
- Added `php artisan laravel-installer:reset` command to quickly clear application caches and remove the installation lock flag. Includes a confirmation warning to prevent accidental resets while safely keeping the license file intact.

### Fixed
- **Critical:** Fixed a route mismatch error (`installer.requirements` instead of `installer.welcome`) in the step sidebar that could cause a `RouteNotFoundException`.
- **Critical:** Mitigated an SQL injection risk in `createDatabase()` by adding regex validation for the database name before running raw queries.
- **High:** Fixed `saveDatabase()` to correctly catch `'missing_database'` connection strings (it previously allowed them to silently bypass the check).
- **High:** Moved `overrideDatabaseConfigs()` from the `register()` method to `boot()` so that database config overrides take precedence and aren't overwritten by other service providers.
- **High:** Improved error detection for migrations and seeders (`runMigrations()` / `runSeeders()`) by checking actual Artisan exit codes instead of parsing output strings.
- **High:** Removed the insecure hardcoded `admin_password` default (`12345678`) from the config, forcing users to explicitly configure this securely.
- **Enhancement:** Removed hardcoded references to the `users` table during initial administrator setup, introducing a new `admin_table` config option (defaults to `users`).
- **Enhancement:** Synchronized all controller references of the `license_check` default fallback to properly match the configuration file (`'optional'`).
