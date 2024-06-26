<?php

require __DIR__ . '/env_loader.php';

$settings = [
    'db' => [
        'user' => getenv('DB_USER'),
        'password' => getenv('DB_PASSWORD'),
        'host' => getenv('DB_HOST'),
        'port' => getenv('DB_PORT'),
        'database' => getenv('DB_DATABASE'),
        'ssl' => [
            'key' => null,
            'cert' => null,
            'ca' => __DIR__ . '/BaltimoreCyberTrustRoot.crt.pem',
        ],
    ],
];

error_log('Database settings: ' . print_r($settings, true));
