<?php

return [
    'provider' => env('FEL_PROVIDER'),
    'username' => env('FEL_USERNAME'),
    'api_key' => env('FEL_KEY'),
    'signature_key' => env('FEL_PASSWORD'),
    'provider_config' => [
        'base_url' => env('FEL_BASE_URL'),
        'certify_url' => env('FEL_CERTIFY_URL'),
        'status_url' => env('FEL_STATUS_URL'),
        'cancel_url' => env('FEL_CANCEL_URL'),
        'timeout' => env('FEL_TIMEOUT', 30),
        'verify_ssl' => env('FEL_VERIFY_SSL', true),
    ]
];