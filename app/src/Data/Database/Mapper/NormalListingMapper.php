<?php
declare(strict_types=1);

namespace App\Data\Database\Mapper;

use App\Domain\Entity\Broadcast;

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
        return new Broadcast(
            $item['id'],
            $this->timeIntMapper->map($item['timeInt']),
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
