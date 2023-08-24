<?php

declare(strict_types=1);

namespace Calien\SecureFilemount\Service;

use Calien\SecureFilemount\Domain\Model\Folder;
use Calien\SecureFilemount\Domain\Repository\FolderRepository;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\Context\UserAspect;
use TYPO3\CMS\Core\Resource\Exception\InvalidPathException;
use TYPO3\CMS\Core\Resource\ProcessedFile;
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
        // if we have a backend call,
        // nevermind, as core does access control,
        // just pass it here
        if (
            GeneralUtility::makeInstance(Context::class)
                ->getPropertyFromAspect('backend.user', 'isLoggedIn')
        ) {
            return true;
        }
        /** @var UserAspect $feUser */
        $feUser = GeneralUtility::makeInstance(Context::class)->getAspect('frontend.user');
        // early exit, if we don't have a frontend login
        if (!$feUser->isLoggedIn()) {
            return false;
        }


        // detect current users groups and
        // folder access groups, if given
        // otherwise look into file storage
        $datasetGroupArray = $feUser->getGroupIds();
        $folderAccess = self::findDirectoryAccess($storage, $identifier);
        $storageRecord = $storage->getStorageRecord();
        $groupArray = $folderAccess ? $folderAccess->getFeGroup() : GeneralUtility::intExplode(',', $storageRecord['fe_group']);

        // check if the user has a related group or enabled for every login
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
        // we're detecting file
        // to identify possible processed
        try {
            $file = $storage->getFile($identifier);
        } catch (\InvalidArgumentException $_) {
            $file = $storage->getFolder($identifier);
        }
        // if the file is processed, e.g. image, get original
        // as we want the access rights of original file
        if ($file instanceof ProcessedFile) {
            $identifier = $file->getOriginalFile()->getIdentifier();
        }

        // identify (parent) folder to get possible access rights
        $currentFolderIdentifier = $storage->getFolderIdentifierFromFileIdentifier($identifier);
        $currentFolder = $storage->getFolder($currentFolderIdentifier);
        $accessibleFolder = GeneralUtility::makeInstance(FolderRepository::class)
            ->findFolderByHash($storage, $currentFolder->getHashedIdentifier());

        // accessible folder dataset not found
        // or no access settings are defined in dataset
        // iterate through parents up to base folder
        if (
            !($accessibleFolder instanceof Folder)
            || !$accessibleFolder->hasAccessDefined()
        ) {
            try {
                $parent = $currentFolder->getParentFolder();
                // getParentFolder gets same folder, if no parent exists.
                // in this case, exit here with null,
                // as no access configuration was found
                if ($parent->getIdentifier() === $currentFolder->getIdentifier()) {
                    return null;
                }
                $accessibleFolder = self::findDirectoryAccess(
                    $storage,
                    $currentFolderIdentifier
                );
            } catch (InvalidPathException $_) {
                return null;
            }
        }
        return $accessibleFolder;
    }
}
