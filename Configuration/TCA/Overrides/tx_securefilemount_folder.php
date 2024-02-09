<?php

declare(strict_types=1);


(static function (): void {
    /**
     * @deprecated will be removed with removal of v11 support
     * cruser_id is breaking removed in v12
     * @see https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/12.0/Breaking-98024-TCA-option-cruserid-removed.html
     */
    if ((new \TYPO3\CMS\Core\Information\Typo3Version())->getMajorVersion() < 12) {
        $GLOBALS['TCA']['tx_securefilemount_folder']['ctrl']['cruser_id'] = 'cruser_id';
    }
})();
