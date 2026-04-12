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
    'license_check' => 'required',

    /*
    |--------------------------------------------------------------------------
    | License Server URL
    |--------------------------------------------------------------------------
    |
    | This value is the URL of the license server where the license key will
    | be validated.
    |
    */
    'license_server_url' => 'https://codekernel.net/api/v1/license',

    /*
    |--------------------------------------------------------------------------
    | License Storage Path
    |--------------------------------------------------------------------------
    |
    | This value is the relative path within the storage directory where the
    | license key will be stored.
    |
    */
    'license_storage_path' => 'app/private/key.private',
];
