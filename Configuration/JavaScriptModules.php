<?php

declare(strict_types=1);

return [
    'dependencies' => ['core', 'backend'],
    'tags' => [
        'backend.contextmenu',
    ],
    'imports' => [
        '@calien/secure-filemount/' => [
            'path' => 'EXT:secure_filemount/Resources/Public/JavaScript/',
        ],
    ],
];
