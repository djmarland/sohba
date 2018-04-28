<?php
declare(strict_types=1);

namespace App\Service;

use App\Domain\Entity\SpecialDay;
use DateTimeInterface;

class SchedulesService extends AbstractService
{
    public function getShowsForDay(int $dayNumber): array
    {
        $results = $this->entityManager->getNormalListingRepo()
            ->findAllForDay($dayNumber);

        return array_map(
            function ($result) {
                return $this->normalBroadcastMapper->map($result);
            },
            $results
        );
    }

    public function getShowsForSpecialDay(SpecialDay $specialDay): array
    {
        $results = $this->entityManager->getSpecialListingRepo()
            ->findAllForLegacySpecialDayId($specialDay->getLegacyId());

        return array_map(
            function ($result) {
                return $this->specialBroadcastMapper->map($result);
            },
            $results
        );
    }

    public function getSpecialDay(DateTimeInterface $date): ?SpecialDay
    {
        $result = $this->entityManager->getSpecialDayRepo()
            ->findForDate($date);

        if ($result) {
            return $this->specialDayMapper->map($result);
        }

        return null;
    }

    public function getAllSpecialDaysAfter(DateTimeInterface $date): array
    {
        $results = $this->entityManager->getSpecialDayRepo()
            ->findAllAfterDate($date);

        return array_map(
            function ($result) {
                return $this->specialDayMapper->map($result);
            },
            $results
        );
    }
}
