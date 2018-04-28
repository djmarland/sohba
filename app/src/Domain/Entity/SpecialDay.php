<?php
declare(strict_types=1);

namespace App\Domain\Entity;

use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

class SpecialDay extends Entity implements \JsonSerializable
{
    private $legacyId;
    private $date;
    private $internalNote;
    private $publicNote;

    public function __construct(
        UuidInterface $id,
        int $legacyId,
        DateTimeImmutable $date,
        ?string $internalNote = null,
        ?string $publicNote = null
    ) {
        parent::__construct($id);
        $this->legacyId = $legacyId;
        $this->date = $date;
        $this->internalNote = $internalNote;
        $this->publicNote = $publicNote;
    }

    public function jsonSerialize()
    {
        return [
            'date' => $this->date->format('c'),
        ];
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getLegacyId(): int
    {
        return $this->legacyId;
    }

    public function getPublicNote(): ?string
    {
        return $this->publicNote;
    }

    public function getInternalNote(): ?string
    {
        return $this->internalNote;
    }
}
