<?php

declare(strict_types=1);

namespace Calien\SecureFilemount\Domain\Repository;

use Calien\SecureFilemount\Domain\Model\Folder;
use Calien\SecureFilemount\Exception\StorageNotFoundException;
use Doctrine\DBAL\Exception;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Resource\Exception\InsufficientFolderAccessPermissionsException;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;

/**
 * @internal Only for usage inside secure_filemount, no public API
 */
final class FolderRepository
{
    public function __construct(
        private readonly StorageRepository $storageRepository
    ) {}

    /**
     * @throws InsufficientFolderAccessPermissionsException
     * @throws Exception
     */
    public function getFolder(\TYPO3\CMS\Core\Resource\Folder $folder): Folder
    {
        $db = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_securefilemount_folder');
        $statement = $db
            ->select('*')
            ->from('tx_securefilemount_folder')
            ->where(
                $db->expr()->eq(
                    'folder',
                    $db->createNamedParameter($folder->getIdentifier())
                ),
                $db->expr()->eq(
                    'storage',
                    $db->createNamedParameter($folder->getStorage()->getUid())
                )
            );
        $result = $statement->executeQuery()->fetchAssociative();
        if ($result === false) {
            return $this->createFolder(
                $folder->getStorage()->getUid(),
                $folder->getIdentifier()
            );
        }

        return new Folder(
            $result['uid'],
            $result['folder'],
            $result['folder_hash'],
            $folder->getStorage(),
            $result['fe_groups']
        );
    }

    /**
     * @throws InsufficientFolderAccessPermissionsException
     * @throws Exception
     */
    public function findByStorageAndPath(int $storageUid, string $path): Folder
    {
        $db = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_securefilemount_folder');
        $statement = $db
            ->select('*')
            ->from('tx_securefilemount_folder')
            ->where(
                $db->expr()->eq('storage', $db->createNamedParameter($storageUid, Connection::PARAM_INT)),
                $db->expr()->eq('folder', $db->createNamedParameter($path))
            );

        $result = $statement->executeQuery()->fetchAssociative();
        if ($result === false) {
            return $this->createFolder($storageUid, $path);
        }

        $resourceStorage = $this->getStorage($storageUid);

        return new Folder(
            $result['uid'],
            $result['folder'],
            $result['folder_hash'],
            $resourceStorage,
            $result['fe_groups']
        );
    }

    /**
     * @throws InsufficientFolderAccessPermissionsException
     */
    public function createFolder(int $storageUid, string $path): Folder
    {
        $storage = $this->getStorage($storageUid);
        $folder = $storage->getFolder($path);
        $newFolderId = StringUtility::getUniqueId('NEW');
        $data['tx_securefilemount_folder'][$newFolderId] = [
            'folder' => $folder->getIdentifier(),
            'folder_hash' => $folder->getHashedIdentifier(),
            'storage' => $storage->getUid(),
            'pid' => 0,
        ];

        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $dataHandler->start($data, []);
        $dataHandler->process_datamap();

        $id = $dataHandler->substNEWwithIDs[$newFolderId];

        return new Folder(
            $id,
            $folder->getIdentifier(),
            $folder->getHashedIdentifier(),
            $storage
        );
    }

    /**
     * @throws Exception
     */
    public function findFolderByHash(ResourceStorage $storage, string $hashIdentifier): ?Folder
    {
        $db = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_securefilemount_folder');
        $statement = $db
            ->select('*')
            ->from('tx_securefilemount_folder')
            ->where(
                $db->expr()->eq(
                    'folder_hash',
                    $db->createNamedParameter($hashIdentifier)
                ),
                $db->expr()->eq(
                    'storage',
                    $db->createNamedParameter($storage->getUid(), Connection::PARAM_INT)
                ),
            );
        $result = $statement->executeQuery()->fetchAssociative();
        if ($result === false) {
            return null;
        }

        return new Folder(
            $result['uid'],
            $result['folder'],
            $result['folder_hash'],
            $this->getStorage((int)$result['storage']),
            $result['fe_groups']
        );
    }

    /**
     * @throws StorageNotFoundException
     */
    private function getStorage(int $uid): ResourceStorage
    {
        $storage = $this->storageRepository->findByUid($uid);
        if (!$storage instanceof ResourceStorage) {
            throw new StorageNotFoundException(
                sprintf('File storage with uid %d was not found.', $uid),
                1753154000
            );
        }

        return $storage;
    }
}
