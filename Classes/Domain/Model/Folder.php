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

    protected array $feGroup = [];

    public function __construct(
        int $uid,
        string $folder,
        string $folderHash,
        ResourceStorage $storage,
        string $feGroup = ''
    ) {
        $this->uid = $uid;
        $this->folder = $folder;
        $this->folderHash = $folderHash;
        $this->storage = $storage;
        $this->feGroup = GeneralUtility::intExplode(',', $feGroup);
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
     * @return array<int, int>
     */
    public function getFeGroup(): array
    {
        return $this->feGroup;
    }
}
