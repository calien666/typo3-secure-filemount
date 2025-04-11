<?php

declare(strict_types=1);

namespace Calien\SecureFilemount\Domain\Model;

use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Folder
{
    /**
     * @var int[]
     */
    protected array $feGroups = [];

    public function __construct(
        protected int $uid,
        protected string $folder,
        protected string $folderHash,
        protected ResourceStorage $storage,
        ?string $feGroups = null
    ) {
        $this->feGroups = $feGroups === null || $feGroups === '' || $feGroups === '0' ? [] : GeneralUtility::intExplode(',', $feGroups, true);
    }

    public function getUid(): int
    {
        return $this->uid;
    }

    public function getFolder(): string
    {
        return $this->folder;
    }

    public function getFolderHash(): string
    {
        return $this->folderHash;
    }

    public function getStorage(): ResourceStorage
    {
        return $this->storage;
    }

    /**
     * @return int[]
     */
    public function getFeGroups(): array
    {
        return $this->feGroups;
    }

    public function hasAccessDefined(): bool
    {
        return $this->feGroups !== [];
    }
}
