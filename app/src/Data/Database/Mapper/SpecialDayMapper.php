<?php
declare(strict_types=1);

namespace App\Data\Database\Mapper;

use App\Domain\Entity\SpecialDay;
use DateTimeImmutable;

class SpecialDayMapper implements MapperInterface
{
    public function map(array $item): SpecialDay
    {
        return new SpecialDay(
            $item['id'],
            $item['pkid'],
            DateTimeImmutable::createFromFormat('jmY', (string) $item['dateInt']),
            $item['internalNote'],
            $item['publicNote']
        );
    }
}
