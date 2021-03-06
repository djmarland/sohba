<?php
declare(strict_types=1);

namespace App\Domain\Entity;

use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

class Image extends Entity implements JsonSerializable
{
    private string $title;
    private ?string $fileName;

    public function __construct(
        UuidInterface $id,
        string $title,
        ?string $fileName = null
    ) {
        parent::__construct($id);
        $this->title = $title;
        $this->fileName = $fileName;
    }

    public function jsonSerialize(): ?array
    {
        return [
            'id' => $this->id,
            'title' => $this->getTitle(),
            'src' => $this->getSrc(),
        ];
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSrc(): string
    {
        return '/images/original/' . $this->fileName;
    }
}
