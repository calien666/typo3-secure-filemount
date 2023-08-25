<?php

(static function (): void {
    $GLOBALS['TYPO3_CONF_VARS']['BE']['ContextMenu']['ItemProviders'][1679000988208]
        = \Calien\SecureFilemount\ContextMenu\ItemProvider::class;

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['Backend\Template\Components\ButtonBar']['getButtonsHook'][1679002016878]
        = \Calien\SecureFilemount\Hooks\ButtonBarHook::class . '->renderButtons';

    $GLOBALS ['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][]
        = \Calien\SecureFilemount\Hooks\DataMapperHook::class;
})();
