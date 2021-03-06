<?php
declare(strict_types=1);

namespace App\Domain\Entity;

use Ramsey\Uuid\UuidInterface;

abstract class Entity
{
    protected UuidInterface $id;

    public function __construct(
        UuidInterface $id
    ) {
        $this->id = $id;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }
}
