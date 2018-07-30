<?php
declare(strict_types=1);

namespace App\Service;

use App\Data\Database\Entity\NormalListing as DbNormalListing;
use App\Data\ID;
use App\Domain\Entity\Broadcast;
use App\Domain\Entity\Programme;
use App\Domain\Entity\SpecialDay;
use App\Domain\ValueObject\Time;
use function App\Functions\DateTimes\isoWeekdayToPHPWeekDay;
use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;

use function App\Functions\DateTimes\weekdayFromDayNum;
use Doctrine\ORM\Query;

class SchedulesService extends AbstractService
{
    public function getShowsForDay(int $dayNumber): array
    {
        return $this->mapMany(
            $this->entityManager->getNormalListingRepo()->findAllForDay(isoWeekdayToPHPWeekDay($dayNumber)),
            $this->normalBroadcastMapper
        );
    }

    public function getShowsForSpecialDay(SpecialDay $specialDay): array
    {
        return $this->mapMany(
            $this->entityManager->getSpecialListingRepo()->findAllForLegacySpecialDayId($specialDay->getLegacyId()),
            $this->specialBroadcastMapper
        );
    }

    public function getListingsForProgramme(Programme $programme): array
    {
        $results = $this->entityManager->getNormalListingRepo()
            ->findAllForLegacyProgrammeId($programme->getLegacyId());

        // todo schema - adjust with updates
        return array_map(
            function ($result) {
                return [
                    'day' => weekdayFromDayNum($result['day']),
                    'time' => $this->timeIntMapper->map($result['timeInt']),
                ];
            },
            $results
        );
    }

    public function getSpecialDay(DateTimeInterface $date): ?SpecialDay
    {
        return $this->mapSingle(
            $this->entityManager->getSpecialDayRepo()->findForDate($date),
            $this->specialDayMapper
        );
    }

    public function getAllSpecialDaysAfter(DateTimeInterface $date): array
    {
        return $this->mapMany(
            $this->entityManager->getSpecialDayRepo()->findAllAfterDate($date),
            $this->specialDayMapper
        );
    }

    public function getNowAndNext(DateTimeImmutable $dateTime): array
    {
        $specialDay = $this->getSpecialDay($dateTime);
        if ($specialDay) {
            $broadcasts = $this->getShowsForSpecialDay($specialDay);
        } else {
            $broadcasts = $this->getShowsForDay((int)$dateTime->format('N'));
        }
        $now = null;
        $next = null;
        $time = new Time((int)$dateTime->format('H'), (int)$dateTime->format('i'));

        foreach ($broadcasts as $i => $broadcast) {
            /** @var $broadcast Broadcast */
            if ($broadcast->getTime()->isBeforeOrAt($time)) {
                // keep overwriting, so the latest is used
                $now = $broadcast;
                $next = null;
                if (isset($broadcasts[$i + 1])) {
                    $next = $broadcasts[$i + 1];
                }
            }
        }

        if (!$next) {
            $tomorrow = $dateTime->add(new DateInterval('P1D'));
            $tomorrow = $tomorrow->setTime(0, 0, 0);
            $broadcasts = $this->getNowAndNext($tomorrow);
            if (isset($broadcasts[0])) {
                $next = $broadcasts[0];
            }
        }

        return [$now, $next];
    }

    public function findUpcomingSports(DateTimeImmutable $now, ?int $limit = null)
    {
        return $this->mapMany(
            $this->entityManager->getSpecialListingRepo()
                ->findListingsOfTypesAfter(Programme::PROGRAMME_SPORTS_TYPES, $now, $limit),
            $this->specialBroadcastMapper
        );
    }

    public function findUpcomingOutsideBroadcasts($now, ?int $limit = null)
    {
        return $this->mapMany(
            $this->entityManager->getSpecialListingRepo()
                ->findListingsOfTypesAfter(Programme::PROGRAMME_OUTSIDE_BROADCASTS_TYPES, $now, $limit),
            $this->specialBroadcastMapper
        );
    }

    public function findNextForProgramme(Programme $programme, DateTimeImmutable $now): ?Broadcast
    {
        return $this->mapSingle(
            $this->entityManager->getSpecialListingRepo()
                ->findNextForLegacyProgrammeId($programme->getLegacyId(), $now),
            $this->specialBroadcastMapper
        );
    }

    public function updateNormalListings(int $day, array $newListings): void
    {
        $this->entityManager->beginTransaction();
        try {
            $this->entityManager->getNormalListingRepo()->deleteAllForDay(
                isoWeekdayToPHPWeekDay($day)
            );
            foreach ($newListings as $listing) {
                $entity = new DbNormalListing(
                    ID::makeNewID(DbNormalListing::class),
                    isoWeekdayToPHPWeekDay($day), // only 0th as far as the database is concerned
                    $listing['time'],
                    $this->entityManager->getProgrammeRepo()->findByLegacyId(
                        $listing['programme'],
                        Query::HYDRATE_OBJECT
                    )
                );
                $this->entityManager->persist($entity);
            }
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }

    public function migrateNormalListings()
    {
        $this->entityManager->getNormalListingRepo()->migrateTimes();
    }
}
