<?php

declare(strict_types=1);

namespace Calien\SecureFilemount\EventListener;

use Calien\SecureFilemount\Domain\Repository\FolderRepository;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\Components\ModifyButtonBarEvent;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * @internal
 * Introduced as ButtonBarHook was removed as breaking change in v12.0
 * @see https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/12.0/Breaking-96806-RemovedHookForModifyingButtonBar.html
 */
final class EditAccessRightsButton
{
    private static string $filePattern = '/(\d)(:)(.*)/';

    public function __invoke(ModifyButtonBarEvent $event): void
    {
        /** @var ServerRequestInterface $request */
        $request = $GLOBALS['TYPO3_REQUEST'];
        $theID = $request->getParsedBody()['id'] ?? $request->getQueryParams()['id'] ?? '';
        if (preg_match(self::$filePattern, $theID, $matches) === false) {
            return;
        }
        if (count($matches) === 0) {
            return;
        }
        $storageId = (int)$matches[1];
        $path = $matches[3];

        $folder = GeneralUtility::makeInstance(FolderRepository::class)
            ->findByStorageAndPath($storageId, $path);

        if ($folder->getStorage()->isPublic()) {
            return;
        }

        $editAccessRightsButton = $event->getButtonBar()->makeLinkButton()
            ->setIcon(
                GeneralUtility::makeInstance(IconFactory::class)
                    ->getIcon('actions-lock', Icon::SIZE_SMALL)
            )
            ->setHref(
                (string)GeneralUtility::makeInstance(UriBuilder::class)
                    ->buildUriFromRoute(
                        'record_edit',
                        [
                            'edit' => [
                                'tx_securefilemount_folder' => [
                                    $folder->getUid() => 'edit',
                                ],
                            ],
                            'returnUrl' => (string)$GLOBALS['TYPO3_REQUEST']->getUri()
                        ]
                    )
            )
            ->setTitle(LocalizationUtility::translate('backend.edit.access', 'secure_filemount') ?? '')
            ->setShowLabelText(true);

        $buttons = $event->getButtons();
        $buttons['left'][][] = $editAccessRightsButton;
        $event->setButtons($buttons);
    }
}
