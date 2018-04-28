<?php
declare(strict_types=1);

namespace App\Data\Database\Mapper;

use App\Domain\ValueObject\Time;

class TimeIntMapper
{
    public function map($timeInt): Time
    {
        // todo - use a proper time object in the database so this isn't needed
        $minutes = $timeInt % 60;
        $hundreds = $timeInt - $minutes;
        $hours = $hundreds / 100;

        return new Time((int) $hours, (int) $minutes);
    }
}
