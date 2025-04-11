<?php

use Calien\SecureFilemount\Middleware\SecureFilemountMiddleware;

return [
    'frontend' => [
        'secure-filemount' => [
            'target' => SecureFilemountMiddleware::class,
            'before' => [
                'typo3/cms-frontend/base-redirect-resolver',
            ],
            'after' => [
                'typo3/cms-frontend/authentication',
            ],
        ],
    ],
];
