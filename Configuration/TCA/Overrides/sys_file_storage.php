<?php

(function () {
    $fe_groups = [
        'fe_groups' => $GLOBALS['TCA']['pages']['columns']['fe_group']
    ];
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('sys_file_storage', $fe_groups);

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
        'sys_file_storage',
        'fe_groups',
        '',
        'after:is_online'
    );
})();
