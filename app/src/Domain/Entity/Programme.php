<?php
declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Entity\Null\NullImage;
use App\Domain\Exception\DataNotFetchedException;
use Ramsey\Uuid\UuidInterface;

class Programme extends Entity implements \JsonSerializable
{
    private $title;
    private $image;
    private $tagLine;
    private $detail;
    private $legacyId;

    public function __construct(
        UuidInterface $id,
        int $legacyId,
        string $title,
        ?string $tagLine,
        ?string $detail,
        ?Image $image = null
    ) {
        parent::__construct($id);
        $this->title = $title;
        $this->image = $image;
        $this->tagLine = $tagLine;
        $this->detail = $detail;
        $this->legacyId = $legacyId;
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

    public function getImage(): ?Image
    {
        if ($this->image === null) {
            throw new DataNotFetchedException(
                'Tried to use the programme image, but it was not fetched'
            );
        }
        if ($this->image instanceof NullImage) {
            return null;
        }
        return $this->image;
    }

    public function getTagLine(): ?string
    {
        return $this->tagLine;
    }

    public function getDetail(): ?string
    {
        return $this->detail;
    }

    public function getAvailableDetail(): ?string
    {
        if (!empty($this->detail)) {
            return $this->detail;
        }
        return $this->tagLine;
    }

    public function getLegacyId(): int
    {
        return $this->legacyId;
    }
}
