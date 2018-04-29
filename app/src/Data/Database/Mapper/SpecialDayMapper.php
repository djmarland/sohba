<?php
declare(strict_types=1);

namespace App\Data\Database\Mapper;

use App\Domain\Entity\SpecialDay;
use DateTimeImmutable;

class SpecialDayMapper implements MapperInterface
{
    public function map(array $item): SpecialDay
    {
        $dateString = str_pad((string) $item['dateInt'], 8, '0', STR_PAD_LEFT);

        return new SpecialDay(
            $item['id'],
            $item['pkid'],
            DateTimeImmutable::createFromFormat('jmY', $dateString),
            $item['internalNote'],
            $item['publicNote']
        );
    }
}
