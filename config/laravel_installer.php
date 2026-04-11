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
    */
    'license_check' => true,

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
