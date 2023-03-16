<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Secure Filemount',
    'description' => 'Allows a secure (non public) filemount for accessing only through logged in fe_user',
    'category' => 'templates',
    'autoload' => [
        'psr-4' => [
            'Calien\\SecureFilemount\\' => 'Classes'
        ],
    ],
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'author' => 'reseen GmbH',
    'author_email' => 'seen@reseen.de',
    'author_company' => 'reseen GmbH',
    'version' => '1.0.0',
];
