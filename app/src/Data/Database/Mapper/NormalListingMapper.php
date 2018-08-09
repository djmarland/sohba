<?php
declare(strict_types=1);

namespace App\Data\Database\Mapper;

use App\Domain\Entity\Broadcast;
use App\Domain\ValueObject\Time;

class NormalListingMapper implements MapperInterface
{
    private $programmeMapper;

    public function __construct(
        ProgrammeMapper $programmeMapper
    ) {
        $this->programmeMapper = $programmeMapper;
    }

    public function map(array $item): Broadcast
    {
        return new Broadcast(
            $item['id'],
            new Time($item['time']),
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
