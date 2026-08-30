<?php

return [
    'client_id' => env('GOBIZ_CLIENT_ID'),
    'client_secret' => env('GOBIZ_CLIENT_SECRET'),
    'partner_id' => env('GOBIZ_PARTNER_ID'),

    'env' => env('GOBIZ_ENV', 'sandbox'),

    'urls' => [
        'sandbox' => [
            'oauth' => 'https://integration-goauth.gojekapi.com',
            'api' => 'https://api.partner-sandbox.gobiz.co.id',
        ],
        'production' => [
            'oauth' => 'https://accounts.go-jek.com',
            'api' => 'https://api.gobiz.co.id',
        ],
    ],
];
