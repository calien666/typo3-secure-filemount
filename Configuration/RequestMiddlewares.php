<?php

return [
    'frontend' => [
        'secure-filemount' => [
            'target' => \Calien\SecureFilemount\Middleware\SecureFilemountMiddleware::class,
            'before' => [
                'typo3/cms-frontend/base-redirect-resolver',
            ],
            'after' => [
                'typo3/cms-frontend/authentication'
            ]
        ]
    ],
];
