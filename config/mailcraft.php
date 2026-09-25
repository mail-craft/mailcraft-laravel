<?php

return [

    /*
    |--------------------------------------------------------------------------
    | MailCraft API Key
    |--------------------------------------------------------------------------
    |
    | Create a key under Settings > API Keys in your MailCraft dashboard.
    | It needs at least the `emails:send` scope to use the Mail transport.
    |
    */

    'api_key' => env('MAILCRAFT_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | API Base URL
    |--------------------------------------------------------------------------
    |
    | Override for self-hosted or staging MailCraft instances.
    |
    */

    'base_url' => env('MAILCRAFT_BASE_URL', 'https://api.mailcraft.host/v1'),

];
