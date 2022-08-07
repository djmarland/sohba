<?php
declare(strict_types=1);

namespace App\Service;

use App\Data\Database\Entity\NormalListing as DbNormalListing;
use App\Data\Database\Entity\SpecialListing as DbSpecialListing;
use App\Domain\Entity\Broadcast;
use App\Domain\Entity\Programme;
use App\Domain\ValueObject\Time;
use Doctrine\ORM\AbstractQuery;
use Exception;
use function App\Functions\DateTimes\isoWeekdayToPHPWeekDay;
use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;

use function App\Functions\DateTimes\weekdayFromDayNum;

class SchedulesService extends AbstractService
{
    public function getShowsForDay(int $dayNumber): array
    {
        return $this->mapMany(
            $this->entityManager->getNormalListingRepo()->findAllForDay(isoWeekdayToPHPWeekDay($dayNumber)),
            $this->normalBroadcastMapper
        );
    }

    public function getShowsForSpecialDate(DateTimeImmutable $specialDate): array
    {
        $midnight = $specialDate->setTime(0, 0, 0);
        return $this->mapMany(
            $this->entityManager->getSpecialListingRepo()->findAllForDate($midnight),
            $this->specialBroadcastMapper
        );
    }

    public function getListingsForProgramme(Programme $programme): array
    {
        $programmeEntity = $this->entityManager->getProgrammeRepo()->getByID(
            $programme->getId(),
            AbstractQuery::HYDRATE_OBJECT
        );

        $results = $this->entityManager->getNormalListingRepo()
            ->findAllForProgramme($programmeEntity);

        return array_map(
            function ($result) {
                return [
                    'day' => weekdayFromDayNum($result['day']),
                    'time' => new Time($result['time']),
                ];
            },
            $results
        );
    }

    public function isSpecialDay(DateTimeImmutable $date): bool
    {
        $start = $date->setTime(0, 0, 0);
        $end = $start->add(new DateInterval('P1D'));

        return !empty($this->entityManager->getSpecialListingRepo()->findDates($start, $end));
    }

    public function getSpecialListingDates(
        ?DateTimeInterface $fromInclusive = null,
        ?DateTimeInterface $toExclusive = null
    ): array {
        return array_map(function ($result) {
            return new DateTimeImmutable($result);
        }, $this->entityManager->getSpecialListingRepo()->findDates($fromInclusive, $toExclusive));
    }

    public function getNowAndNext(DateTimeImmutable $dateTime): array
    {
        if ($this->isSpecialDay($dateTime)) {
            $broadcasts = $this->getShowsForSpecialDate($dateTime);
        } else {
            $broadcasts = $this->getShowsForDay((int)$dateTime->format('N'));
        }
        $now = null;
        $next = null;
        $time = new Time($dateTime);

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

    public function findUpcomingSports(DateTimeImmutable $now, ?int $limit = null): array
    {
        return $this->mapMany(
            $this->entityManager->getSpecialListingRepo()
                ->findListingsOfTypesAfter(Programme::PROGRAMME_SPORTS_TYPES, $now, $limit),
            $this->specialBroadcastMapper
        );
    }

    public function findUpcomingOutsideBroadcasts(DateTimeImmutable $now, ?int $limit = null): array
    {
        return $this->mapMany(
            $this->entityManager->getSpecialListingRepo()
                ->findListingsOfTypesAfter(Programme::PROGRAMME_OUTSIDE_BROADCASTS_TYPES, $now, $limit),
            $this->specialBroadcastMapper
        );
    }

    public function findNextForProgramme(Programme $programme, DateTimeImmutable $now): ?Broadcast
    {
        $programmeEntity = $this->entityManager->getProgrammeRepo()->getByID(
            $programme->getId(),
            AbstractQuery::HYDRATE_OBJECT
        );

        return $this->mapSingle(
            $this->entityManager->getSpecialListingRepo()
                ->findNextForProgramme($programmeEntity, $now),
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
                    isoWeekdayToPHPWeekDay($day), // only 0th as far as the database is concerned
                    $listing['time'],
                    $this->entityManager->getProgrammeRepo()->getByID(
                        $listing['programmeId'],
                        AbstractQuery::HYDRATE_OBJECT
                    )
                );
                $this->entityManager->persist($entity);
            }
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }

    public function deleteSpecialBetween(DateTimeImmutable $date, DateTimeImmutable $endDate): void
    {
        $this->entityManager->getSpecialListingRepo()
            ->deleteBetween($date, $endDate);
    }

    public function updateSpecialListings(DateTimeImmutable $date, array $newListings): void
    {
        $from = $date->setTime(0, 0, 0);
        $end = $date->add(new DateInterval('P1D'));
        $this->entityManager->beginTransaction();
        try {
            $this->deleteSpecialBetween($from, $end);

            foreach ($newListings as $listing) {
                $entity = new DbSpecialListing(
                    $listing['time'],
                    $this->entityManager->getProgrammeRepo()->getByID(
                        $listing['programme'],
                        AbstractQuery::HYDRATE_OBJECT
                    )
                );
                $entity->internalNote = $listing['internalNote'];
                $entity->publicNote = $listing['publicNote'];

                $this->entityManager->persist($entity);
            }

            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }
}
