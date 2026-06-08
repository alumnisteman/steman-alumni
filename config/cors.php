<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'https://alumni-steman.my.id',
        'https://www.alumni-steman.my.id',
        'https://admin.alumni-steman.my.id',
        'https://api.alumni-steman.my.id',
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 3600,
    'supports_credentials' => true,
];
