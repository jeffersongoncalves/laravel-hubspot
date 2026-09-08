<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Private App Access Token
    |--------------------------------------------------------------------------
    |
    | Create one under Settings > Integrations > Private Apps and grant it the
    | CRM scopes you need. Sent as `Authorization: Bearer {token}`.
    |
    */
    'token' => env('HUBSPOT_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | API Base URL
    |--------------------------------------------------------------------------
    */
    'base_url' => env('HUBSPOT_BASE_URL', 'https://api.hubapi.com'),

    /*
    |--------------------------------------------------------------------------
    | Rate Limit Retries
    |--------------------------------------------------------------------------
    |
    | HubSpot allows 100 requests per 10 seconds. A 429 response is retried
    | this many times, waiting `retry_delay` milliseconds between attempts.
    |
    */
    'retry_times' => env('HUBSPOT_RETRY_TIMES', 3),
    'retry_delay' => env('HUBSPOT_RETRY_DELAY', 1000),

];
