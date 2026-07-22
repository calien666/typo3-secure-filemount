<?php

declare(strict_types=1);

namespace Calien\SecureFilemount\Tests\Functional\Service;

use Calien\SecureFilemount\Service\FolderAccessService;
use Calien\SecureFilemount\Tests\Functional\AbstractFolderAccessTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Resource\StorageRepository;

final class FolderAccessServiceTest extends AbstractFolderAccessTestCase
{
    /**
     * @param int[] $frontendGroupIds
     */
    #[Test]
    #[DataProvider('accessMatrix')]
    public function checkResourceAccessEnforcesTheGate(
        string $loginMode,
        int $frontendUserUid,
        array $frontendGroupIds,
        string $identifier,
        bool $expected
    ): void {
        match ($loginMode) {
            'anonymous' => $this->actAsAnonymous(),
            'backend' => $this->actAsBackendUser(),
            'frontend' => $this->actAsFrontendUser($frontendUserUid, $frontendGroupIds),
            default => self::fail(sprintf('Unknown login mode "%s".', $loginMode)),
        };

        $storage = $this->get(StorageRepository::class)->getStorageObject(1);

        self::assertSame(
            $expected,
            $this->get(FolderAccessService::class)->checkResourceAccess($storage, $identifier)
        );
    }

    public static function accessMatrix(): \Generator
    {
        yield 'anonymous visitor is denied' => [
            'loginMode' => 'anonymous',
            'frontendUserUid' => 0,
            'frontendGroupIds' => [],
            'identifier' => '/secure/restricted/doc.txt',
            'expected' => false,
        ];
        yield 'backend user is granted regardless of groups' => [
            'loginMode' => 'backend',
            'frontendUserUid' => 0,
            'frontendGroupIds' => [],
            'identifier' => '/secure/restricted/doc.txt',
            'expected' => true,
        ];
        yield 'frontend user without matching group is denied' => [
            'loginMode' => 'frontend',
            'frontendUserUid' => 2,
            'frontendGroupIds' => [9],
            'identifier' => '/secure/restricted/doc.txt',
            'expected' => false,
        ];
        yield 'frontend user with matching group is granted' => [
            'loginMode' => 'frontend',
            'frontendUserUid' => 2,
            'frontendGroupIds' => [1],
            'identifier' => '/secure/restricted/doc.txt',
            'expected' => true,
        ];
        yield 'any-login folder (-2) grants any authenticated user' => [
            'loginMode' => 'frontend',
            'frontendUserUid' => 2,
            'frontendGroupIds' => [9],
            'identifier' => '/secure/anylogin/doc.txt',
            'expected' => true,
        ];
        yield 'child file inherits granting parent folder access' => [
            'loginMode' => 'frontend',
            'frontendUserUid' => 2,
            'frontendGroupIds' => [1],
            'identifier' => '/secure/restricted/deep/report.pdf',
            'expected' => true,
        ];
        yield 'child file inherits denying parent folder access' => [
            'loginMode' => 'frontend',
            'frontendUserUid' => 2,
            'frontendGroupIds' => [9],
            'identifier' => '/secure/restricted/deep/report.pdf',
            'expected' => false,
        ];
    }

    /**
     * The file lives in a folder with no tx_securefilemount_folder record on any ancestor, so
     * access must fall back to the storage-level fe_groups. A user in the storage group is granted.
     */
    #[Test]
    public function storageLevelFallbackGrantsMatchingGroup(): void
    {
        $this->actAsFrontendUser(2, [1]);

        $storage = $this->get(StorageRepository::class)->getStorageObject(1);

        self::assertTrue(
            $this->get(FolderAccessService::class)->checkResourceAccess($storage, '/secure/fallback/file.txt')
        );
    }

    /**
     * Same storage-level fallback, but the user is not a member of the storage group and is denied.
     */
    #[Test]
    public function storageLevelFallbackDeniesForeignGroup(): void
    {
        $this->actAsFrontendUser(2, [9]);

        $storage = $this->get(StorageRepository::class)->getStorageObject(1);

        self::assertFalse(
            $this->get(FolderAccessService::class)->checkResourceAccess($storage, '/secure/fallback/file.txt')
        );
    }

    #[Test]
    public function getProtectedFileStoragesReturnsOnlineNonPublicStorage(): void
    {
        $storages = $this->get(FolderAccessService::class)->getProtectedFileStorages();

        self::assertCount(1, $storages);
        self::assertSame(1, $storages[0]->getUid());
    }
}
