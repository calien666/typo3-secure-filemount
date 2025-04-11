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

final class FolderAccessService
{
    public function __construct(
        protected FolderRepository $folderRepository,
        protected StorageRepository $storageRepository,
        protected Context $context
    ) {}

    /**
     * @return ResourceStorage[]
     */
    public function getProtectedFileStorages(): array
    {
        $foundStorages = [];
        $storages = $this->storageRepository->findAll();
        foreach ($storages as $storage) {
            if ($storage->isOnline() && !$storage->isPublic()) {
                $foundStorages[] = $storage;
            }
        }

        return  $foundStorages;
    }

    /**
     * @throws AspectNotFoundException
     */
    public function checkResourceAccess(ResourceStorage $storage, string $identifier): bool
    {
        // if we have a backend call,
        // nevermind, as core does access control,
        // just pass it here
        if ($this->context->getPropertyFromAspect('backend.user', 'isLoggedIn')) {
            return true;
        }

        /** @var UserAspect $feUser */
        $feUser = $this->context->getAspect('frontend.user');
        // early exit, if we don't have a frontend login
        if (!$feUser->isLoggedIn()) {
            return false;
        }

        // detect current users groups and
        // folder access groups, if given
        // otherwise look into file storage
        $datasetGroupArray = $feUser->getGroupIds();
        $folderAccess = $this->findDirectoryAccess($storage, $identifier);
        $storageRecord = $storage->getStorageRecord();
        $groupArray = $folderAccess instanceof Folder
            ? $folderAccess->getFeGroups()
            : GeneralUtility::intExplode(',', $storageRecord['fe_group']);

        // check if the user has a related group or enabled for every login
        $groupArrayCount = count($groupArray);
        return in_array(-2, $groupArray, true) ||
        count(array_diff($groupArray, $feUser->getGroupIds())) < $groupArrayCount ||
        count(array_diff($groupArray, $datasetGroupArray)) < $groupArrayCount;
    }

    private function findDirectoryAccess(ResourceStorage $storage, string $identifier): ?Folder
    {
        // we're detecting file
        // to identify possible processed
        try {
            $file = $storage->getFile($identifier);
        } catch (\InvalidArgumentException) {
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
        $accessibleFolder = $this->folderRepository->findFolderByHash($storage, $currentFolder->getHashedIdentifier());

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

                $accessibleFolder = $this->findDirectoryAccess(
                    $storage,
                    $currentFolderIdentifier
                );
            } catch (InvalidPathException) {
                return null;
            }
        }

        return $accessibleFolder;
    }
}
