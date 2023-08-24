<?php

declare(strict_types=1);

namespace Calien\SecureFilemount\Service;

use Calien\SecureFilemount\Domain\Model\Folder;
use Calien\SecureFilemount\Domain\Repository\FolderRepository;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\Context\UserAspect;
use TYPO3\CMS\Core\Resource\Exception\InvalidPathException;
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
     * @throws AspectNotFoundException
     */
    public static function checkResourceAccess(ResourceStorage $storage, string $identifier): bool
    {
        if (
            GeneralUtility::makeInstance(Context::class)
                ->getPropertyFromAspect('backend.user', 'isLoggedIn')
        ) {
            return true;
        }
        /** @var UserAspect $feUser */
        $feUser = GeneralUtility::makeInstance(Context::class)->getAspect('frontend.user');
        if (!$feUser->isLoggedIn()) {
            return false;
        }

        $datasetGroupArray = $feUser->getGroupIds();
        $folderAccess = self::findDirectoryAccess($storage, $identifier);
        $storageRecord = $storage->getStorageRecord();
        $groupArray = $folderAccess ? $folderAccess->getFeGroup() : GeneralUtility::intExplode(',', $storageRecord['fe_group']);

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

    private static function findDirectoryAccess(ResourceStorage $storage, string $identifier): ?Folder
    {
        $currentFolderIdentifier = $storage->getFolderIdentifierFromFileIdentifier($identifier);
        $currentFolder = $storage->getFolder($currentFolderIdentifier);
        $accessibleFolder = GeneralUtility::makeInstance(FolderRepository::class)
            ->findFolderByHash($storage, $currentFolder->getHashedIdentifier());
        if (!($accessibleFolder instanceof Folder)) {
            try {
                $accessibleFolder = self::findDirectoryAccess($storage, $currentFolderIdentifier);
            } catch (InvalidPathException $_) {
                return null;
            }
        }
        return $accessibleFolder;
    }
}
