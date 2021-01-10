<?php
declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Exception\DataNotFetchedException;
use App\Domain\ValueObject\Time;
use InvalidArgumentException;
use JsonSerializable;
use function App\Functions\DateTimes\formatDateForDisplay;
use function App\Functions\DateTimes\formatShortDateForDisplay;
use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

class Broadcast extends Entity implements JsonSerializable
{
    private ?Programme $programme;
    private Time $time;
    private ?string $publicNote;
    private ?string $internalNote;
    private ?DateTimeImmutable $date;

    public function __construct(
        UuidInterface $id,
        Time $time,
        ?string $publicNote,
        ?string $internalNote,
        ?DateTimeImmutable $date = null,
        Programme $programme = null
    ) {
        parent::__construct($id);
        $this->programme = $programme;
        $this->time = $time;
        $this->publicNote = $publicNote;
        $this->internalNote = $internalNote;
        $this->date = $date;
    }

    public function jsonSerialize(): ?array
    {
        $data = [
            'id' => $this->id,
            'time' => $this->time,
            'internalNote' => $this->internalNote, // we never use JSON outside admin
            'publicNote' => $this->publicNote,
        ];

        if ($this->programme) {
            $data['programme'] = $this->programme;
        }

        return $data;
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

    public function getDate(): DateTimeImmutable
    {
        if ($this->date === null) {
            throw new InvalidArgumentException(
                'This broadcast does not have a date'
            );
        }
        return $this->date;
    }

    public function getDateFormatted(): string
    {
        return formatShortDateForDisplay($this->getDate());
    }

    public function getDateFormattedFull(): string
    {
        return formatDateForDisplay($this->getDate());
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

    public function getNotes(): ?string
    {
        if (!$this->publicNote && !$this->internalNote) {
            return null;
        }
        return implode(' - ', array_filter([
            $this->publicNote,
            $this->internalNote,
        ]));
    }
}
