<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Unlayer Configuration
    |--------------------------------------------------------------------------
    |
    | Define your Unlayer Project ID here to load your custom Unlayer designs,
    | images, or custom configurations. Leave empty or null to use the free
    | embed.js version without saving designs to Unlayer servers.
    |
    */
    'unlayer_project_id' => env('UNLAYER_PROJECT_ID', null),

    /*
    |--------------------------------------------------------------------------
    | Notification Overrides
    |--------------------------------------------------------------------------
    |
    | When set to true, the plugin will automatically override the default 
    | Laravel Verify Email and Reset Password notifications to use 
    | templates from the database (auth.verify_email, auth.reset_password).
    |
    */
    'register_notifications' => env('FILAMENT_EMAIL_TEMPLATES_OVERRIDE_AUTH', true),
];
