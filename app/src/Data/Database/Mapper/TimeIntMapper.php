<?php
declare(strict_types=1);

namespace App\Data\Database\Mapper;

use App\Domain\ValueObject\Time;

class TimeIntMapper
{
    public function map($timeInt): Time
    {
        $timeString = str_pad((string) $timeInt, 4, '0', STR_PAD_LEFT);
        return new Time((int) substr($timeString, 0, 2), (int) substr($timeString, 2, 2));
    }
}
