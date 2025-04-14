<?php

declare(strict_types=1);

namespace Calien\SecureFilemount\EventListener;

use ApacheSolrForTypo3\Solr\Event\Indexing\BeforeDocumentIsProcessedForIndexingEvent;
use Calien\SecureFilemount\Domain\Model\Folder;
use Calien\SecureFilemount\Exception\InvalidDocumentException;
use Calien\SecureFilemount\Service\FolderAccessService;
use TYPO3\CMS\Core\Resource\ResourceFactory;

final class PreAddModifyDocuments
{
    public function __construct(
        protected FolderAccessService $accessService,
        protected ResourceFactory $resourceFactory
    ) {}

    public function __invoke(BeforeDocumentIsProcessedForIndexingEvent $event): void
    {
        if ($event->getIndexQueueItem()->getType() !== 'sys_file_metadata') {
            return;
        }

        foreach ($event->getDocuments() as $document) {
            $accessGroups = '';
            $fields = $document->getFields();

            if (!array_key_exists('uid', $fields) || !array_key_exists('access', $fields)) {
                throw new InvalidDocumentException(
                    'The current document is missing the uid or access property',
                    1744626349
                );
            }

            $fileObject = $this->resourceFactory->getFileObject((int)$fields['uid']);
            $folderAccess = $this->accessService->findDirectoryAccess($fileObject->getStorage(), $fileObject->getIdentifier());

            if ($folderAccess instanceof Folder) {
                $accessGroups = implode(',', $folderAccess->getFeGroups());
            } else {
                $accessGroups = $fileObject->getStorage()->getStorageRecord()['fe_groups'];
            }

            if ($accessGroups === '' || $accessGroups === '0') {
                return;
            }

            $access = ((string)$fields['uid']) . ':' . $accessGroups . '/' . $fields['access'];

            $document->setField('access', $access);
        }
    }
}
