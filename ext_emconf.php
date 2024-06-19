<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Secure Filemount',
    'description' => 'Allows a secure (non public) filemount for accessing only through logged in fe_user',
    'category' => 'services',
    'autoload' => [
        'psr-4' => [
            'Calien\\SecureFilemount\\' => 'Classes',
        ],
    ],
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-12.4.99',
            'php' => '7.4.0-8.3.99',
        ],
        'conflicts' => [
            'fal_securedownload' => '',
            'secure_downloads' => '',
            'fal_protect' => '',
        ],
    ],
    'state' => 'stable',
    'author' => 'Markus Hofmann',
    'author_email' => 'typo3@calien.de',
    'author_company' => '',
    'version' => '1.1.2',
];
