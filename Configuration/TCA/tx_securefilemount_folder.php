<?php

declare(strict_types=1);

return [
    'ctrl' => [
        'title' => 'LLL:EXT:secure_filemount/Resources/Private/Language/locallang.xlf:folder',
        'label' => 'folder',
        'iconfile' => 'EXT:secure_filemount/Resources/Public/Icons/Extension.svg',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'hideTable' => true,
        'rootLevel' => true,
        'default_sortby' => 'ORDER BY folder ASC',
        'security' => [
            'ignoreWebMountRestriction' => true,
            'ignoreRootLevelRestriction' => true,
        ],
        'versioningWS' => false,
        'searchFields' => '',
    ],
    'inferface' => [
        'showRecordFieldList' => '',
        'maxDBListItems' => 20,
        'maxSingleDBListItems' => 100,
    ],
    'types' => [
        '0' => ['showitem' => 'fe_group,--palette--;;filePalette'],
    ],
    'palettes' => [
        // File palette, hidden but needs to be included all the time
        'filePalette' => [
            'showitem' => 'storage,folder,folder_hash',
            'isHiddenPalette' => true,
        ],
    ],
    'columns' => [
        'storage' => [
            'label' => 'LLL:EXT:secure_filemount/Resources/Private/Language/locallang_db.xlf:folder.storage',
            'config' => [
                'type' => 'group',
                'size' => 1,
                'maxitems' => 1,
                'minitems' => 1,
                'allowed' => 'sys_file_storage',
            ],
        ],
        'folder' => [
            'label' => 'LLL:EXT:secure_filemount/Resources/Private/Language/locallang.xlf:folder.folder',
            'config' => [
                'type' => 'input',
                'eval' => 'required',
            ],
        ],
        'folder_hash' => [
            'label' => 'LLL:EXT:secure_filemount/Resources/Private/Language/locallang.xlf:folder.folder_hash',
            'config' => [
                'type' => 'input',

            ],
        ],
        'fe_group' => $GLOBALS['TCA']['pages']['columns']['fe_group'],
    ],
];
