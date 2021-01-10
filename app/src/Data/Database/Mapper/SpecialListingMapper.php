<?php
declare(strict_types=1);

namespace App\Data\Database\Mapper;

use App\Domain\Entity\Broadcast;
use App\Domain\Entity\Programme;
use App\Domain\ValueObject\Time;
use DateTimeZone;

class SpecialListingMapper implements MapperInterface
{
    private ProgrammeMapper $programmeMapper;

    public function __construct(
        ProgrammeMapper $programmeMapper
    ) {
        $this->programmeMapper = $programmeMapper;
    }

    public function map(array $item): Broadcast
    {
        $date = $item['dateTimeUk'];
        $ukTime = $item['dateTimeUk']->setTimezone(new DateTimeZone('Europe/London'));
        $time = new Time($ukTime);

        return new Broadcast(
            $item['id'],
            $time,
            !empty($item['publicNote']) ? $item['publicNote'] : null,
            !empty($item['internalNote']) ? $item['internalNote'] : null,
            $date,
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
}
