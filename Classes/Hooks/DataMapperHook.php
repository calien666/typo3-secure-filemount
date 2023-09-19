<?php

declare(strict_types=1);

namespace Calien\SecureFilemount\Hooks;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\DataHandling\DataHandler;

/**
 * @internal
 */
final class DataMapperHook
{
    public function processDatamap_afterDatabaseOperations(
        $status,
        $table,
        $id,
        array $fieldArray,
        DataHandler $dataHandler
    ): void {
        if ($table === 'tx_securefilemount_folder') {
            BackendUtility::setUpdateSignal('updateFolderTree');
        }
    }
}
