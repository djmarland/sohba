<?php
declare(strict_types=1);

namespace App\Data\Database\Entity;

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
    public $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    public $uuid;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    public $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    public $updatedAt;

    public function __construct(
        UuidInterface $id
    ) {
        $this->id = $id;
        $this->uuid = (string)$this->id;
    }
}