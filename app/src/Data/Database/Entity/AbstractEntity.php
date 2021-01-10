<?php
declare(strict_types=1);

namespace App\Data\Database\Entity;

use App\Data\ID;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\MappedSuperclass
 */
abstract class AbstractEntity
{
    /**
     * @ORM\Column(type="uuid_binary")
     */
    public UuidInterface $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    public string $uuid;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=false)
     */
    public ?DateTimeImmutable $createdAt = null;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=false)
     */
    public ?DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->id = ID::makeNewID(static::class);
        $this->uuid = (string)$this->id;
    }
}
