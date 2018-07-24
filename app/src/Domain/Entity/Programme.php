<?php
declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Entity\Null\NullImage;
use App\Domain\Exception\DataNotFetchedException;
use Ramsey\Uuid\UuidInterface;

class Programme extends Entity implements \JsonSerializable
{
    public const PROGRAMME_TYPE_REGULAR = 0;
    public const PROGRAMME_TYPE_CRICKET = 1;
    public const PROGRAMME_TYPE_FOOTBALL = 2;
    public const PROGRAMME_TYPE_EVENT = 3;
    public const PROGRAMME_TYPE_SPECIAL = 4;

    public const PROGRAMME_EVENT_TYPES = [
        self::PROGRAMME_TYPE_CRICKET => 'Cricket',
        self::PROGRAMME_TYPE_FOOTBALL => 'Football',
        self::PROGRAMME_TYPE_EVENT => 'Event',
        self::PROGRAMME_TYPE_SPECIAL => 'Special Broadcast',
    ];

    public const PROGRAMME_SPORTS_TYPES = [
        self::PROGRAMME_TYPE_CRICKET,
        self::PROGRAMME_TYPE_FOOTBALL,
    ];

    public const PROGRAMME_OUTSIDE_BROADCASTS_TYPES = [
        self::PROGRAMME_TYPE_EVENT,
        self::PROGRAMME_TYPE_SPECIAL,
    ];

    private $title;
    private $image;
    private $tagLine;
    private $detail;
    private $legacyId;
    private $programmeType;

    public function __construct(
        UuidInterface $id,
        int $legacyId,
        string $title,
        int $programmeType,
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
        $this->programmeType = $programmeType;
    }

    public static function getAllTypesMapped()
    {
        $types = [];
        foreach (array_merge(
                     [self::PROGRAMME_TYPE_REGULAR => 'Regular'],
                     self::PROGRAMME_EVENT_TYPES
                 ) as $id => $title) {
            $types[] = [
                'id' => $id,
                'title' => $title,
            ];
        }
        return $types;
    }

    public function jsonSerialize()
    {
        $data = [
            'id' => $this->id,
            'legacyId' => $this->legacyId,
            'title' => $this->getTitle(),
            'tagLine' => $this->tagLine,
            'type' => $this->programmeType,
            'typeTitle' => $this->getTypeName(),
            'detail' => $this->detail,
        ];
        if ($this->image) {
            $data['image'] = $this->image;
        }

        return $data;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getLetterGroup(): string
    {
        $letter = \strtoupper(\substr($this->title, 0, 1));
        if (\ctype_alpha($letter)) {
            return $letter;
        }
        return '#';
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

    public function getType(): int
    {
        return $this->programmeType;
    }

    public function getUrl(): string
    {
        return '/programmes/' . $this->legacyId;
    }

    public function getTypeName(): ?string
    {
        return self::PROGRAMME_EVENT_TYPES[$this->programmeType] ?? null;
    }

    public function isEvent(): bool
    {
        return array_key_exists($this->programmeType, self::PROGRAMME_EVENT_TYPES);
    }

    public function isCricket(): bool
    {
        return $this->programmeType === self::PROGRAMME_TYPE_CRICKET;
    }
}
