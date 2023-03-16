<?php

(function () {
    $fe_group = [
        'fe_group' => $GLOBALS['TCA']['pages']['columns']['fe_group']
    ];
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('sys_file_storage', $fe_group);

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
        'sys_file_storage',
        'fe_group',
        '',
        'after:is_online'
    );
})();
