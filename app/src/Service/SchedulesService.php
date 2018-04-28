<?php
declare(strict_types=1);

namespace App\Service;

use App\Domain\Entity\Broadcast;
use App\Domain\Entity\SpecialDay;
use App\Domain\ValueObject\Time;
use DateInterval;
use DateTimeImmutable;
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

    public function getNowAndNext(DateTimeImmutable $dateTime): array
    {
        $specialDay = $this->getSpecialDay($dateTime);
        if ($specialDay) {
            $broadcasts = $this->getShowsForSpecialDay($specialDay);
        } else {
            $broadcasts = $this->getShowsForDay((int) $dateTime->format('w'));
        }
        $now = null;
        $next = null;
        $time = new Time((int) $dateTime->format('H'), (int) $dateTime->format('i'));

        foreach ($broadcasts as $i => $broadcast) {
            /** @var $broadcast Broadcast */
            if ($broadcast->getTime()->isBeforeOrAt($time)) {
                // keep overwriting, so the latest is used
                $now = $broadcast;
                if (isset($broadcasts[$i+1])) {
                    $next = $broadcasts[$i+1];
                }
            }
        }

        if (!$next) {
            $tomorrow = $dateTime->add(new DateInterval('P1D'));
            $tomorrow = $tomorrow->setTime(0,0,0);
            $broadcasts = $this->getNowAndNext($tomorrow);
            if (isset($broadcasts[0])) {
                $next = $broadcasts[0];
            }
        }

        return [$now, $next];
    }
}
