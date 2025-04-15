<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use ApacheSolrForTypo3\Solr\Event\Indexing\BeforeDocumentIsProcessedForIndexingEvent;
use Calien\SecureFilemount\EventListener\PreAddModifyDocuments;
use Symfony\Component\DependencyInjection\ContainerBuilder;

return function (ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder) {
    $services = $containerConfigurator
        ->services();

    /**
     * Check if BeforeDocumentIsProcessedForIndexingEvent is defined, which means that EXT:solr is available.
     */
    if ($containerBuilder->hasDefinition(BeforeDocumentIsProcessedForIndexingEvent::class)) {
        $services->set(PreAddModifyDocuments::class)
            ->tag(
                'event.listener',
                [
                    'identifier' => 'secure-filemount.access',
                    'event' => BeforeDocumentIsProcessedForIndexingEvent::class,
                ]
            );
    }
};
