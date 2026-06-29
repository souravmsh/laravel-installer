# Changelog

All notable changes to `souravmsh/laravel-installer` will be documented in this file.

## [1.4.0] - 2026-06-29

### Changed
- **`laravel-installer:reset` command overhauled** — now runs three sequential steps:
  1. **Fix permissions**: `chmod 664` on `.env`; `chmod 775` (recursive) on `storage/`, `bootstrap/cache/`, and `storage/app/private/`. Missing directories are created automatically. Reports per-path success/failure so ownership issues are immediately visible.
  2. **Remove lock files**: Resolves lock-file paths from config *before* clearing caches (prevents stale in-memory config from targeting the wrong path). Removes both `key.install` and `key.private` if present. Prints resolved paths for transparency.
  3. **Clear all caches**: Replaced individual `cache:clear` / `config:clear` / `route:clear` / `view:clear` calls with a single `optimize:clear`, which also flushes the compiled bootstrap cache (`bootstrap/cache/config.php`) — the root cause of the previous "not moving to setup mode" bug.
- Added a final warning when `INSTALLER_ENABLED=false` is detected in `.env`, since removing the lock file alone cannot restore setup mode in that state.

## [1.3.0] - 2026-06-29


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
