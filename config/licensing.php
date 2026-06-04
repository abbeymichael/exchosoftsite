<?php

return [

    /*
    |--------------------------------------------------------------------------
    | License Signing Key
    |--------------------------------------------------------------------------
    |
    | This key is used to sign license payloads (HMAC SHA-256).
    | DO NOT expose this key to clients.
    |
    */

    'signing_key' => env('LICENSING_SIGNING_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Global License Constants
    |--------------------------------------------------------------------------
    |
    | These values are fixed for all products, plans, and licenses.
    | They do not need to be configured per product or plan.
    |
    | offline_ttl_hours  - How many hours a license can be used offline
    |                      before it must re-validate. (default: 168 = 7 days)
    |
    | grace_period_days  - How many days after expiry a license still works
    |                      before fully expiring. (default: 7)
    |
    | max_activations    - Default maximum devices per license when a plan
    |                      does not specify its own limit. (default: 1)
    |
    */

    'offline_ttl_hours' => (int) env('LICENSE_OFFLINE_TTL_HOURS', 168),
    'grace_period_days' => (int) env('LICENSE_GRACE_PERIOD_DAYS', 7),
    'max_activations'   => (int) env('LICENSE_MAX_ACTIVATIONS', 1),
    'default_edition'   => env('LICENSE_DEFAULT_EDITION', 'standard'),
    'support_url'      => env('APP_SUPPORT_URL', 'https://support.exchosoft.com'),
    'renewal_url'      => env('APP_RENEWAL_URL', 'https://exchosoft.com/renew'),

];
