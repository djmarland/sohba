<?php
declare(strict_types=1);

namespace App\Domain\Entity;

use Ramsey\Uuid\UuidInterface;

class Image extends Entity implements \JsonSerializable
{
    private $title;
    private $legacyID;
    private $fileName;

    public function __construct(
        UuidInterface $id,
        int $legacyID,
        string $title,
        ?string $fileName = null
    ) {
        parent::__construct($id);
        $this->title = $title;
        $this->legacyID = $legacyID;
        $this->fileName = $fileName;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->legacyID,
            'title' => $this->getTitle(),
            'src' => $this->getSrc(),
        ];
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getLegacyID(): int
    {
        return $this->legacyID;
    }

    public function getSrc(): string
    {
        if ($this->fileName) {
            return '/images/original/' . $this->fileName;
        }
        return '/image.php?i=' . $this->legacyID;
    }
}
