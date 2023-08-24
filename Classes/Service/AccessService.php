<?php

declare(strict_types=1);

namespace Calien\SecureFilemount\Service;

use Doctrine\DBAL\Driver\Exception;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\Context\UserAspect;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AccessService
{
    /**
     * @return ResourceStorage[]
     */
    public static function getProtectedFileStorages(): array
    {
        $foundStorages = [];
        $storages = GeneralUtility::makeInstance(StorageRepository::class)->findAll();
        foreach ($storages as $storage) {
            if ($storage->isOnline() && $storage->getStorageRecord()['fe_group'] != 0) {
                $foundStorages[] = $storage;
            }
        }

        return  $foundStorages;
    }

    /**
     * @param ResourceStorage $storage
     * @return bool
     * @throws AspectNotFoundException
     * @throws Exception
     */
    public static function checkStorageAccess(ResourceStorage $storage): bool
    {
        /** @var UserAspect $feUser */
        $feUser = GeneralUtility::makeInstance(Context::class)->getAspect('frontend.user');
        if (!$feUser->isLoggedIn()) {
            return false;
        }

        $storageRecord = $storage->getStorageRecord();

        $groupArray = GeneralUtility::intExplode(',', $storageRecord['fe_group']);

        $datasetGroupArray = [];
        $db = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('fe_users');
        $fetch = $db
            ->select(
                ['*'],
                'fe_users',
                ['uid' => $feUser->get('id')]
            )->fetchAssociative();
        if ($fetch) {
            $datasetGroupArray = GeneralUtility::trimExplode(',', $fetch['usergroup']);
        }

        // check if user has group or enabled for every login
        if (
            in_array(-2, $groupArray) ||
            count(array_diff($groupArray, $feUser->getGroupIds())) < count($groupArray) ||
            count(array_diff($groupArray, $datasetGroupArray)) < count($groupArray)
        ) {
            return true;
        }
        return false;
    }
}
