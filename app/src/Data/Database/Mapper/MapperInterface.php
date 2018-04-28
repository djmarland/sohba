<?php
declare(strict_types=1);

namespace App\Data\Database\Mapper;

interface MapperInterface
{
    public function map(array $item);
}
