<?php
declare(strict_types=1);

namespace App\Data\Database\Mapper;

use App\Domain\Entity\Entity;

interface MapperInterface
{
    public function map(array $item);
}
