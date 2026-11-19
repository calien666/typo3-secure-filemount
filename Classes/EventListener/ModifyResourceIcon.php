<?php

declare(strict_types=1);

namespace Calien\SecureFilemount\EventListener;

use Calien\SecureFilemount\Domain\Repository\FolderRepository;
use TYPO3\CMS\Core\Imaging\Event\ModifyIconForResourcePropertiesEvent;

/**
 * @internal
 */
final class ModifyResourceIcon
{
    public function __construct(
        private readonly FolderRepository $folderRepository,
    ) {}

    public function __invoke(ModifyIconForResourcePropertiesEvent $event): void
    {
        $resource = $event->getResource();
        $folder = $this->folderRepository->findFolderByHash($resource->getStorage(), $resource->getHashedIdentifier());
        if ($folder === null) {
            return;
        }

        if (count($folder->getFeGroups()) === 0) {
            return;
        }

        if (count($folder->getFeGroups()) === 1 && $folder->getFeGroups()[0] === 0) {
            return;
        }

        $event->setOverlayIdentifier('status-user-group-frontend');
    }
}
