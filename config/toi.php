<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | The name of your application used in the default views and branding.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Logo URL
    |--------------------------------------------------------------------------
    |
    | URL or path to the application logo displayed in authentication views.
    | Set to null to display the application name as text instead.
    |
    */

    'logo' => null,

    /*
    |--------------------------------------------------------------------------
    | Default Dashboard Route
    |--------------------------------------------------------------------------
    |
    | The URI where the admin dashboard is accessible after authentication.
    |
    */

    'dashboard_uri' => 'admin',

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | The fully qualified class name of the User model used for authentication.
    |
    */

    'user_model' => App\Models\User::class,

];
