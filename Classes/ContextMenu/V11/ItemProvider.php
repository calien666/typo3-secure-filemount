<?php

declare(strict_types=1);

namespace Calien\SecureFilemount\ContextMenu\V11;

use Calien\SecureFilemount\Domain\Repository\FolderRepository;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Exception;
use TYPO3\CMS\Backend\ContextMenu\ItemProviders\AbstractProvider;
use TYPO3\CMS\Core\Resource\Exception\InsufficientFolderAccessPermissionsException;
use TYPO3\CMS\Core\Resource\Exception\ResourceDoesNotExistException;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\FolderInterface;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal
 * @deprecated will be removed when v11 support dropped
 * Migration needed, as ItemProvider registry changed in v12.0
 * @see https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/12.0/Breaking-96333-AutoConfigurationOfContextMenuItemProviders.html
 */
final class ItemProvider extends AbstractProvider
{
    protected ResourceFactory $resourceFactory;

    protected ?Folder $folder = null;

    /**
     * @var array<string, mixed>
     */
    protected array $addItemConfiguration = [
        'permissions_divider' => [
            'type' => 'divider',
        ],
        'permissions' => [
            'label' => 'LLL:EXT:secure_filemount/Resources/Private/Language/locallang.xlf:backend.edit.access',
            'iconIdentifier' => 'actions-lock',
            'callbackAction' => 'editRecord',
        ],
    ];

    /**
     * ItemProvider constructor.
     * @param ResourceFactory|null $resourceFactory
     */
    public function __construct(
        string $table,
        string $identifier,
        string $context = '',
        ResourceFactory $resourceFactory = null
    ) {
        $this->resourceFactory = $resourceFactory
            ?? GeneralUtility::makeInstance(ResourceFactory::class);
        parent::__construct($table, $identifier, $context);
        // add own items to the default
        $this->itemsConfiguration = array_merge_recursive(
            $this->itemsConfiguration,
            $this->addItemConfiguration
        );
    }

    public function getPriority(): int
    {
        return 90;
    }

    public function canHandle(): bool
    {
        return $this->table === 'sys_file' || $this->table === 'sys_file_storage';
    }

    /**
     * @throws ResourceDoesNotExistException
     */
    public function addItems(array $items): array
    {
        $this->initialize();
        if (!$this->folder instanceof Folder) {
            return $items;
        }
        $items += $this->prepareItems($this->itemsConfiguration);
        return $items;
    }

    /**
     * @throws ResourceDoesNotExistException
     */
    protected function initialize(): void
    {
        parent::initialize();
        $resource = $this->resourceFactory
            ->retrieveFileOrFolderObject($this->identifier);

        if ($resource instanceof Folder
            && !$resource->getStorage()->isPublic()
            && in_array(
                $resource->getRole(),
                [FolderInterface::ROLE_DEFAULT, FolderInterface::ROLE_USERUPLOAD],
                true
            )
        ) {
            $this->folder = $resource;
        }
    }

    /**
     * @return array{
     *     data-callback-module: string,
     *     data-folder-record-uid: int,
     *     data-storage: int,
     *     data-folder: string,
     *     data-folder-hash: string
     *     }
     * @throws InsufficientFolderAccessPermissionsException
     * @throws Exception
     * @throws DBALException
     */
    protected function getAdditionalAttributes(string $itemName): array
    {
        $utility = GeneralUtility::makeInstance(FolderRepository::class);
        $folderRecord = $utility->getFolder($this->folder);

        return [
            'data-callback-module' => 'TYPO3/CMS/SecureFilemount/ContextMenuActions',
            'data-folder-record-uid' => $folderRecord ? $folderRecord->getUid() : 0,
            'data-storage' => $this->folder->getStorage()->getUid(),
            'data-folder' => $this->folder->getIdentifier(),
            'data-folder-hash' => $this->folder->getHashedIdentifier(),
        ];
    }
}
