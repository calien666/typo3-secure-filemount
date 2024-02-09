<?php

declare(strict_types=1);

namespace Calien\SecureFilemount\Domain\Model;

use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Folder
{
    protected int $uid;

    protected string $folder;

    protected string $folderHash;

    protected ResourceStorage $storage;

    /**
     * @var int[]
     */
    protected array $feGroups = [];

    public function __construct(
        int $uid,
        string $folder,
        string $folderHash,
        ResourceStorage $storage,
        ?string $feGroups = null
    ) {
        $this->uid = $uid;
        $this->folder = $folder;
        $this->folderHash = $folderHash;
        $this->storage = $storage;
        $this->feGroups = !empty($feGroups) ? GeneralUtility::intExplode(',', $feGroups, true) : [];
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
        return count($this->feGroups) > 0;
    }
}
