<?php

declare(strict_types=1);

namespace Calien\SecureFilemount\Tests\Functional\EventListener;

use Calien\SecureFilemount\Tests\Functional\AbstractFolderAccessTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Resource\StorageRepository;

final class ModifyResourceIconTest extends AbstractFolderAccessTestCase
{
    #[Test]
    #[DataProvider('folderOverlays')]
    public function folderIconCarriesTheFrontendGroupOverlay(string $folderIdentifier, ?string $expectedOverlay): void
    {
        $folder = $this->get(StorageRepository::class)->getStorageObject(1)->getFolder($folderIdentifier);

        $icon = $this->get(IconFactory::class)->getIconForResource($folder);

        self::assertSame($expectedOverlay, $icon->getOverlayIcon()?->getIdentifier());
    }

    public static function folderOverlays(): \Generator
    {
        yield 'folder restricted to a frontend group' => [
            'folderIdentifier' => '/secure/restricted/',
            'expectedOverlay' => 'status-user-group-frontend',
        ];
        yield 'folder without access record' => [
            'folderIdentifier' => '/secure/fallback/',
            'expectedOverlay' => null,
        ];
    }
}
