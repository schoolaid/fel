<?php

return [
    'provider' => env('FEL_PROVIDER'),
    'username' => env('FEL_USERNAME'),
    // ATENCIÓN: los nombres internos api_key/signature_key están
    // históricamente INVERTIDOS respecto a lo que se envía a INFILE
    // (ver InfileProvider y FelConfig::getLlaveFirma/getLlaveApi):
    //   api_key       -> header llaveFirma      = LLAVE DEL FIRMADOR
    //   signature_key -> header llaveApi/llave  = LLAVE DEL API REST
    // Se conservan así por compatibilidad con los consumidores existentes.
    // Las variables de entorno usan los nombres veraces; los legados
    // FEL_KEY (llave de firma) y FEL_PASSWORD (llave del API) siguen
    // soportados como respaldo.
    'api_key' => env('FEL_LLAVE_FIRMA', env('FEL_KEY')),
    'signature_key' => env('FEL_LLAVE_API', env('FEL_PASSWORD')),
    'provider_config' => [
        'base_url' => env('FEL_BASE_URL'),
        'certify_url' => env('FEL_CERTIFY_URL'),
        'status_url' => env('FEL_STATUS_URL'),
        'cancel_url' => env('FEL_CANCEL_URL'),
        'timeout' => env('FEL_TIMEOUT', 30),
        'verify_ssl' => env('FEL_VERIFY_SSL', true),
        'identifier' => env('FEL_IDENTIFIER'),
    ]
];
