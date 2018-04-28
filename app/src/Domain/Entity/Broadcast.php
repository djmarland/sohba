<?php
declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Exception\DataNotFetchedException;
use App\Domain\ValueObject\Time;
use Ramsey\Uuid\UuidInterface;

class Broadcast extends Entity implements \JsonSerializable
{
    private $programme;
    private $time;
    private $publicNote;
    private $internalNote;

    public function __construct(
        UuidInterface $id,
        Time $time,
        ?string $publicNote,
        ?string $internalNote,
        Programme $programme = null
    ) {
        parent::__construct($id);
        $this->programme = $programme;
        $this->time = $time;
        $this->publicNote = $publicNote;
        $this->internalNote = $internalNote;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
        ];
    }

    public function getProgramme(): ?Programme
    {
        if ($this->programme === null) {
            throw new DataNotFetchedException(
                'Tried to use the broadcast programme, but it was not fetched'
            );
        }
        return $this->programme;
    }

    public function getTime(): Time
    {
        return $this->time;
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
