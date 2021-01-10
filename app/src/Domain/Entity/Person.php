<?php
declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Entity\Null\NullImage;
use App\Domain\Exception\DataNotFetchedException;
use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

class Person extends Entity implements JsonSerializable
{
    private string $name;
    private ?Image $image;
    private bool $isOnCommittee;
    private ?string $committeeTitle;
    private ?int $committeeOrder;

    public function __construct(
        UuidInterface $id,
        string $name,
        bool $isOnCommittee,
        ?string $committeeTitle,
        ?int $committeeOrder,
        ?Image $image = null
    ) {
        parent::__construct($id);
        $this->name = $name;
        $this->image = $image;
        $this->isOnCommittee = $isOnCommittee;
        $this->committeeTitle = $committeeTitle;
        $this->committeeOrder = $committeeOrder;
    }

    public function jsonSerialize(): ?array
    {
        $data = [
            'id' => $this->id,
            'isOnCommittee' => $this->isOnCommittee,
            'committeeTitle' => $this->committeeTitle,
            'committeeOrder' => $this->committeeOrder,
            'name' => $this->getName(),
        ];
        if ($this->image) {
            $data['image'] = $this->image;
        }

        return $data;
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

    public function getCommitteeOrder(): ?int
    {
        return $this->committeeOrder;
    }
}
