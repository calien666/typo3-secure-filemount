<?php

declare(strict_types=1);

namespace Calien\SecureFilemount\EventListener;

use TYPO3\CMS\Core\Resource\Event\GeneratePublicUrlForResourceEvent;

class GeneratePublicUrlForSecureFilemount
{
    public function __invoke(GeneratePublicUrlForResourceEvent $event): void
    {
        $storage = $event->getStorage();
        if (!$storage->isPublic() && $storage->isOnline()) {
            $resource = $event->getResource();
            $identifier = $resource->getIdentifier();
            $config = $storage->getConfiguration();
            $publicUrl = sprintf('%s%s', rtrim($config['baseUri'] ?? '', '/'), $identifier);
            $event->setPublicUrl($publicUrl);
        }
    }
}
