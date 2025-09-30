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
            'typo3' => '12.4.0-13.4.99',
            'php' => '8.1.0-8.4.99',
        ],
        'conflicts' => [
            'fal_securedownload' => '',
            'secure_downloads' => '',
            'fal_protect' => '',
        ],
        'suggests' => [
            'solr' => '12.0.0-13.9.99',
        ],
    ],
    'state' => 'stable',
    'author' => 'Markus Hofmann',
    'author_email' => 'typo3@calien.de',
    'author_company' => '',
    'version' => '2.0.0',
];
