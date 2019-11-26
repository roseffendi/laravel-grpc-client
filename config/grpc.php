<?php

return [
    'services' => [
        'Protobuf\\Identity\\AuthServiceClient' => [
            'host' => env('AUTH_SERVICE_HOST'),
            'authentication' => 'tls', // insecure, tls
            'cert' => env('AUTH_SERVICE_CERT')
        ],
    ],
];