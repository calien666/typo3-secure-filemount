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
            'typo3' => '13.4.0-14.3.99',
            'php' => '8.2.0-8.5.99',
        ],
        'conflicts' => [
            'fal_securedownload' => '',
            'secure_downloads' => '',
            'fal_protect' => '',
        ],
        'suggests' => [
            'solr' => '13.0.0-14.9.99',
        ],
    ],
    'state' => 'stable',
    'author' => 'Markus Hofmann',
    'author_email' => 'typo3@calien.de',
    'author_company' => '',
    'version' => '3.0.0',
];
