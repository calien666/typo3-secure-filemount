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
    /**
     * @param array<string, mixed> $fieldArray
     */
    public function processDatamap_afterDatabaseOperations(
        string $status,
        string $table,
        string|int $id,
        array $fieldArray,
        DataHandler $dataHandler
    ): void {
        if ($table === 'tx_securefilemount_folder') {
            BackendUtility::setUpdateSignal('updateFolderTree');
        }
    }
}
