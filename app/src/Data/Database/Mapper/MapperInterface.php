<?php
declare(strict_types=1);

namespace App\Data\Database\Mapper;

interface MapperInterface
{
    /**
     * @return mixed
     */
    public function map(array $item);
}
