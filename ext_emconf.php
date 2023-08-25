<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Secure Filemount',
    'description' => 'Allows a secure (non public) filemount for accessing only through logged in fe_user',
    'category' => 'templates',
    'autoload' => [
        'psr-4' => [
            'Calien\\SecureFilemount\\' => 'Classes',
        ],
    ],
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'author' => 'Markus Hofmann',
    'author_email' => 'typo3@calien.de',
    'author_company' => '',
    'version' => '1.0.0',
];
