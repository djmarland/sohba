<?php
declare(strict_types=1);

namespace App\Domain\Entity;

use Ramsey\Uuid\UuidInterface;

class Image extends Entity implements \JsonSerializable
{
    private $title;
    private $legacyID;

    public function __construct(
        UuidInterface $id,
        int $legacyID,
        string $title
    ) {
        parent::__construct($id);
        $this->title = $title;
        $this->legacyID = $legacyID;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'title' => $this->getTitle(),
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
        return '/image.php?i=' . $this->legacyID;
    }
}
