<?php

declare(strict_types=1);

namespace Calien\SecureFilemount\Hooks;

use Calien\SecureFilemount\Domain\Repository\FolderRepository;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Exception;
use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Resource\Exception\InsufficientFolderAccessPermissionsException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * @internal
 * @deprecated will be removed when v11 support dropped
 * @see @see https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/12.0/Breaking-96806-RemovedHookForModifyingButtonBar.html
 */
final class ButtonBarHook
{
    private static string $filePattern = '/(\d)(:)(.*)/';

    /**
     * @param array<int|string, mixed> $params
     * @return array<int|string, mixed>
     *
     * @throws DBALException
     * @throws Exception
     * @throws RouteNotFoundException
     * @throws InsufficientFolderAccessPermissionsException
     */
    public function renderButtons(array $params, ButtonBar $buttonBar): array
    {
        $theID = GeneralUtility::_GP('id') ?? '';
        $buttons = $params['buttons'];
        if (preg_match(self::$filePattern, $theID, $matches) === false) {
            return $buttons;
        }
        if (count($matches) === 0) {
            return $buttons;
        }
        $storageId = (int)$matches[1];
        $path = $matches[3];

        $folder = GeneralUtility::makeInstance(FolderRepository::class)
            ->findByStorageAndPath($storageId, $path);

        if ($folder->getStorage()->isPublic()) {
            return $buttons;
        }

        $editButton = $buttonBar->makeLinkButton()
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

        $buttons[ButtonBar::BUTTON_POSITION_LEFT][5][] = $editButton;

        return $buttons;
    }
}
