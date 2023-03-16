<?php

declare(strict_types=1);

namespace Calien\SecureFilemount\ContextMenu;

use Calien\SecureFilemount\Domain\Repository\FolderRepository;
use TYPO3\CMS\Backend\ContextMenu\ItemProviders\AbstractProvider;
use TYPO3\CMS\Core\Resource\Exception\ResourceDoesNotExistException;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\FolderInterface;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ItemProvider extends AbstractProvider
{
    protected ResourceFactory $resourceFactory;

    protected Folder $folder;

    /**
     * @var array<string, mixed>
     */
    protected array $addItemsConfiguration = [
        'permissions_divider' => [
            'type' => 'divider',
        ],
        'permissions' => [
            'label' => 'LLL:EXT:fal_securedownload/Resources/Private/Language/locallang_be.xlf:clickmenu.folderpermissions',
            'iconIdentifier' => 'actions-lock',
            'callbackAction' => 'editRecord'
        ]
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
            $this->addItemsConfiguration
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
    protected function initialize()
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

    protected function getAdditionalAttributes(string $itemName): array
    {
        $utility = GeneralUtility::makeInstance(FolderRepository::class);
        $folderRecord = $utility->getFolder($this->folder);

        return [
            'data-callback-module' => 'TYPO3/CMS/FalSecuredownload/ContextMenuActions',
            'data-folder-record-uid' => $folderRecord->getUid() ?? 0,
            'data-storage' => $this->folder->getStorage()->getUid(),
            'data-folder' => $this->folder->getIdentifier(),
            'data-folder-hash' => $this->folder->getHashedIdentifier(),
        ];
    }
}
