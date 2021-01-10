<?php
declare(strict_types=1);

namespace App\Domain\Entity;

use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

class PageCategory extends Entity implements JsonSerializable
{
    private string $title;

    public function __construct(
        UuidInterface $id,
        string $title
    ) {
        parent::__construct($id);
        $this->title = $title;
    }

    public function equals(PageCategory $entity): bool
    {
        return $this->getId()->equals($entity->getId());
    }

    public function jsonSerialize(): ?array
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
}
