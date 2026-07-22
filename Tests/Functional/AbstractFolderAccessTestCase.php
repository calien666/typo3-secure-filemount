<?php

declare(strict_types=1);

namespace Calien\SecureFilemount\Tests\Functional;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\UserAspect;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Shared bootstrap for the access-gate functional tests.
 *
 * Provides a single non-public, online Local storage (uid 1) with a real file tree on disk and
 * helpers to drive the Context login aspects the way FolderAccessService reads them. The service
 * only inspects Context aspects, so no real fe_users/be_users rows or sessions are required.
 */
abstract class AbstractFolderAccessTestCase extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'calien/secure-filemount',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '/Fixtures/sys_file_storage.csv');
        $this->importCSVDataSet(__DIR__ . '/Fixtures/tx_securefilemount_folder.csv');
        $this->createSecureFileTree();
    }

    /**
     * Creates the physical files the storage driver and file indexer need on disk. The storage
     * basePath is fileadmin/, so identifiers like /secure/restricted/doc.txt map below fileadmin/.
     */
    private function createSecureFileTree(): void
    {
        $basePath = Environment::getPublicPath() . '/fileadmin';
        $files = [
            '/secure/restricted/doc.txt' => 'restricted',
            '/secure/restricted/deep/report.pdf' => 'deep',
            '/secure/anylogin/doc.txt' => 'anylogin',
            '/secure/fallback/file.txt' => 'fallback',
        ];
        foreach ($files as $identifier => $content) {
            $absolutePath = $basePath . $identifier;
            GeneralUtility::mkdir_deep(dirname($absolutePath) . '/');
            file_put_contents($absolutePath, $content);
        }
    }

    /**
     * @param int[] $groupIds
     */
    protected function actAsFrontendUser(int $uid, array $groupIds): void
    {
        $frontendUser = new FrontendUserAuthentication();
        $frontendUser->user = ['uid' => $uid];

        $context = GeneralUtility::makeInstance(Context::class);
        $context->setAspect('frontend.user', new UserAspect($frontendUser, $groupIds));
        $context->setAspect('backend.user', new UserAspect(new BackendUserAuthentication()));
    }

    protected function actAsBackendUser(): void
    {
        $backendUser = new BackendUserAuthentication();
        $backendUser->user = ['uid' => 1];

        $context = GeneralUtility::makeInstance(Context::class);
        $context->setAspect('backend.user', new UserAspect($backendUser));
        $context->setAspect('frontend.user', new UserAspect(new FrontendUserAuthentication()));
    }

    protected function actAsAnonymous(): void
    {
        $context = GeneralUtility::makeInstance(Context::class);
        $context->setAspect('frontend.user', new UserAspect(new FrontendUserAuthentication()));
        $context->setAspect('backend.user', new UserAspect(new BackendUserAuthentication()));
    }
}
