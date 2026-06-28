<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */
    'app_name' => env('APP_NAME', 'Laravel Application'),

    'installer_enabled' => env('INSTALLER_ENABLED', true),

    'installed_key_path' => env('INSTALLER_KEY_PATH', 'app/private/key.install'),

    /*
    |--------------------------------------------------------------------------
    | License Check
    |--------------------------------------------------------------------------
    |
    | This value determines if the installer should check for a valid license
    | key before allowing the installation to proceed.
    |
    | Supported options:
    | - 'required': License key must be validated before installation.
    | - 'optional': License check step is shown but can be skipped.
    | - 'disabled': License check step is skipped entirely.
    |
    */
    'license_check' => env('INSTALLER_LICENSE', 'optional'),

    /*
    |--------------------------------------------------------------------------
    | License Server URL
    |--------------------------------------------------------------------------
    |
    | This value is the URL of the license server where the license key will
    | be validated.
    |
    */
    'license_server_url' => env('INSTALLER_LICENSE_URL', 'https://codekernel.net/api/v1/license'),

    /*
    |--------------------------------------------------------------------------
    | License Storage Path
    |--------------------------------------------------------------------------
    |
    | This value is the relative path within the storage directory where the
    | license key will be stored.
    |
    */
    'license_storage_path' => env('INSTALLER_LICENSE_KEY_PATH', 'app/private/key.private'),

    /*
    |--------------------------------------------------------------------------
    | Admin Credentials
    |--------------------------------------------------------------------------
    |
    | These credentials will be used to create the initial administrator
    | account if it does not already exist after the database is set up.
    |
    | WARNING: Never leave admin_password without a value set in your .env file.
    |
    */
    'admin_email' => env('INSTALLER_ADMIN_EMAIL', 'superadmin@codekernel.net'),
    'admin_password' => env('INSTALLER_ADMIN_PASSWORD', '12345678'),

    /*
    |--------------------------------------------------------------------------
    | Admin Table
    |--------------------------------------------------------------------------
    |
    | The database table that holds administrator/user accounts. Change this
    | if your application uses a custom table name (e.g. 'admins', 'members').
    |
    */
    'admin_table' => env('INSTALLER_ADMIN_TABLE', 'users'),
];

