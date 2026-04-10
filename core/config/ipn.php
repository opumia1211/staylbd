<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Optional shared HMAC for POST payment IPN endpoints
    |--------------------------------------------------------------------------
    |
    | When IPN_HMAC_SECRET is set, POST (and PUT/PATCH) requests to /ipn/*
    | must send header:
    |
    |   X-Ipn-Signature: <lowercase hex HMAC-SHA256 of the raw request body>
    |
    | Leave empty for PayPal IPN, many Stripe flows, and other gateways that
    | verify inside their ProcessController instead.
    |
    */

    'hmac_secret' => env('IPN_HMAC_SECRET', ''),

];
