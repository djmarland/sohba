<?php
declare(strict_types=1);

namespace App\Data\Database\Mapper;

use App\Domain\Entity\Broadcast;
use App\Domain\ValueObject\Time;

class NormalListingMapper implements MapperInterface
{
    private $programmeMapper;
    private $timeIntMapper;

    public function __construct(
        ProgrammeMapper $programmeMapper,
        TimeIntMapper $timeIntMapper
    ) {
        $this->programmeMapper = $programmeMapper;
        $this->timeIntMapper = $timeIntMapper;
    }

    public function map(array $item): Broadcast
    {
        if ($item['time'] instanceof \DateTimeImmutable) {
            // todo schema - remove this check and make the column not NULLABLE
            $time = new Time(
                (int)$item['time']->format('H'),
                (int)$item['time']->format('i')
            );
        } else {
            $time = $this->timeIntMapper->map($item['timeInt']);
        }

        return new Broadcast(
            $item['id'],
            $time,
            null,
            null,
            null,
            $this->mapProgramme($item)
        );
    }

    private function mapProgramme(array $item)
    {
        if (array_key_exists('programme', $item) && isset($item['programme'])) {
            return $this->programmeMapper->map($item['programme']);
        }
        return null;
    }
}
