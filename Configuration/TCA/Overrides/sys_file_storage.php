<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

(function (): void {
    $fe_groups = [
        'fe_groups' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.fe_group',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'size' => 7,
                'maxitems' => 20,
                'items' => [
                    [
                        'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.any_login',
                        'value' => -2,
                    ],
                    [
                        'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.usergroups',
                        'value' => '--div--',
                    ],
                ],
                'exclusiveKeys' => '-2',
                'foreign_table' => 'fe_groups',
            ],
        ],
    ];
    ExtensionManagementUtility::addTCAcolumns('sys_file_storage', $fe_groups);

    ExtensionManagementUtility::addToAllTCAtypes(
        'sys_file_storage',
        'fe_groups',
        '',
        'after:is_online'
    );
})();
