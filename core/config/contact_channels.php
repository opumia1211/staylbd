<?php

return [

    'whatsapp' => [

        /*
        | Non-production only: ignored in production (HMAC is always enforced when secrets exist).
        */
        'bypass_signature' => env('WHATSAPP_WEBHOOK_BYPASS_SIGNATURE', false),

        /*
        | Encrypted per integration: auth_meta key `whatsapp_app_secret` (Meta App Secret).
        | Used to verify X-Hub-Signature-256 on inbound webhooks.
        */
        'signature_header' => 'X-Hub-Signature-256',
    ],

];
