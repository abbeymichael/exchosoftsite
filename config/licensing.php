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

];
