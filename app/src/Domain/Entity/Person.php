<?php
declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Entity\Null\NullImage;
use App\Domain\Exception\DataNotFetchedException;
use Ramsey\Uuid\UuidInterface;

class Person extends Entity implements \JsonSerializable
{
    private $name;
    private $image;
    private $isOnCommittee;
    private $committeeTitle;
    private $legacyId;

    public function __construct(
        UuidInterface $id,
        int $legacyId,
        string $name,
        bool $isOnCommittee,
        ?string $committeeTitle,
        ?Image $image = null
    ) {
        parent::__construct($id);
        $this->name = $name;
        $this->image = $image;
        $this->isOnCommittee = $isOnCommittee;
        $this->committeeTitle = $committeeTitle;
        $this->legacyId = $legacyId;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->getName(),
        ];
    }

    public function getName(): string
    {
        return $this->name;
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

    public function isOnCommittee(): bool
    {
        return $this->isOnCommittee;
    }

    public function getCommitteeTitle(): ?string
    {
        return $this->committeeTitle;
    }

    public function getLegacyId(): int
    {
        return $this->legacyId;
    }
}
