<?php

return [
    'duitku' => [
        'merchant_code' => env('DUITKU_MERCHANT_CODE', ''),
        'merchant_key' => env('DUITKU_MERCHANT_KEY', ''),
        'api_version' => env('DUITKU_API_VERSION', 'v2'),
        'api_url' => env('DUITKU_API_URL', 'https://api-sandbox.duitku.com'),
        'sandbox' => env('DUITKU_SANDBOX', true),
    ],
];
