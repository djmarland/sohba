<?php
declare(strict_types=1);

namespace App\Domain\Entity\Null;

trait NullBaseTrait
{
    public function jsonSerialize(): ?array
    {
        return null;
    }
}
