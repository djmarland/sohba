<?php
declare(strict_types=1);

namespace App\Domain\Entity;

use Ramsey\Uuid\UuidInterface;

class PageCategory extends Entity implements \JsonSerializable
{
    private $title;
    private $legacyId;

    public function __construct(
        UuidInterface $id,
        int $legacyId,
        string $title
    ) {
        parent::__construct($id);
        $this->title = $title;
        $this->legacyId = $legacyId;
    }

    public function equals(PageCategory $entity): bool
    {
        // todo - move to ID check
        return $this->getTitle() === $entity->getTitle();
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'title' => $this->getTitle(),
            'legacyId' => $this->getLegacyId(),
        ];
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getLegacyId(): int
    {
        return $this->legacyId;
    }
}
