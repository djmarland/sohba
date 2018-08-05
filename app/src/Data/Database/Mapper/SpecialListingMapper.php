<?php
declare(strict_types=1);

namespace App\Data\Database\Mapper;

use App\Domain\Entity\Broadcast;
use App\Domain\Entity\Programme;
use App\Domain\ValueObject\Time;

class SpecialListingMapper implements MapperInterface
{
    private $programmeMapper;
    private $timeIntMapper;
    private $specialDayMapper;

    public function __construct(
        ProgrammeMapper $programmeMapper,
        SpecialDayMapper $specialDayMapper,
        TimeIntMapper $timeIntMapper
    ) {
        $this->programmeMapper = $programmeMapper;
        $this->timeIntMapper = $timeIntMapper;
        $this->specialDayMapper = $specialDayMapper;
    }

    public function map(array $item): Broadcast
    {
        // todo schema - remove this check and make the column not NULLABLE
        if ($item['dateTimeUk'] instanceof \DateTimeImmutable) {
            $ukTime = $item['dateTimeUk']->setTimezone(new \DateTimeZone('Europe/London'));
            $time = new Time(
                (int)$ukTime->format('H'),
                (int)$ukTime->format('i')
            );
        } else {
            $time = $this->timeIntMapper->map($item['timeInt']);
        }

        return new Broadcast(
            $item['id'],
            $time,
            !empty($item['publicNote']) ? $item['publicNote'] : null,
            !empty($item['internalNote']) ? $item['internalNote'] : null,
            $this->mapDate($item),
            $this->mapProgramme($item)
        );
    }

    private function mapProgramme(array $item): ?Programme
    {
        if (array_key_exists('programme', $item) && isset($item['programme'])) {
            return $this->programmeMapper->map($item['programme']);
        }
        return null;
    }

    private function mapDate(array $item)
    {
        if (array_key_exists('specialDay', $item)) {
            return $this->specialDayMapper->map($item['specialDay'])->getDate();
        }
        return null;
    }
}
