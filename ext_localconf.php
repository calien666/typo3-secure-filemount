<?php

(static function (): void {
    $typo3Version = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Information\Typo3Version::class);
    if ($typo3Version->getVersion() < 12) {
        $GLOBALS['TYPO3_CONF_VARS']['BE']['ContextMenu']['ItemProviders'][1679000988208]
            = \Calien\SecureFilemount\ContextMenu\V11\ItemProvider::class;

        // Will be removed when v11 support dropped
        // @see @see https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/12.0/Breaking-96806-RemovedHookForModifyingButtonBar.html
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['Backend\Template\Components\ButtonBar']['getButtonsHook'][1679002016878]
            = \Calien\SecureFilemount\Hooks\ButtonBarHook::class . '->renderButtons';
    }

    $GLOBALS ['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][]
        = \Calien\SecureFilemount\Hooks\DataMapperHook::class;
})();
