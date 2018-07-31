<?php
declare(strict_types=1);

namespace App\Data\Database\EntityRepository;

use App\Data\Database\Entity\SpecialListing;
use DateTimeImmutable;
use Doctrine\ORM\Query;

class SpecialListingRepository extends AbstractEntityRepository
{
    public function findAllForLegacySpecialDayId(
        int $specialDayId,
        $resultType = Query::HYDRATE_ARRAY
    ) {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl', 'programme', 'image')
            ->where('IDENTITY(tbl.specialDay) = :specialDayId')
            ->innerJoin('tbl.programme', 'programme')
            ->leftJoin('programme.image', 'image')
            ->orderBy('tbl.timeInt', 'ASC')
            ->setParameter('specialDayId', $specialDayId);
        return $qb->getQuery()->getResult($resultType);
    }

    public function findListingsOfTypesAfter(
        array $programmeTypes,
        DateTimeImmutable $after,
        int $limit = null,
        $resultType = Query::HYDRATE_ARRAY
    ) {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl', 'programme', 'spd')
            ->innerJoin('tbl.specialDay', 'spd')
            ->innerJoin('tbl.programme', 'programme')
            ->where('spd.timestamp >= :after')
            ->andWhere('programme.type IN (:types)')
            ->orderBy('spd.timestamp', 'ASC')
            ->addOrderBy('tbl.timeInt', 'ASC')
            ->setParameter('after', $after->getTimestamp())
            ->setParameter('types', $programmeTypes);

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult($resultType);
    }

    public function findNextForLegacyProgrammeId(
        int $getLegacyId,
        DateTimeImmutable $now,
        $resultType = Query::HYDRATE_ARRAY
    ) {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl', 'spd')
            ->innerJoin('tbl.specialDay', 'spd')
            ->where('spd.timestamp >= :after')
            ->andWhere('IDENTITY(tbl.programme) = :programmeId')
            ->setMaxResults(1)
            ->setParameter('after', $now->getTimestamp())
            ->setParameter('programmeId', $getLegacyId);

        return $qb->getQuery()->getOneOrNullResult($resultType);
    }

    public function findDates(
        ?\DateTimeInterface $after = null
    ): array {
        if ($after) {
            die('todo'); // todo
        }

        $qb = $this->createQueryBuilder('tbl')
            ->select('DISTINCT(tbl.dateUtc)')
            ->orderBy('tbl.dateUtc', 'ASC');

        return array_map(function($result) {
            return reset($result);
        }, $qb->getQuery()->getResult(Query::HYDRATE_ARRAY));
    }

    public function migrate(): void
    {

        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl', 'date')
            ->leftJoin('tbl.specialDay', 'date')
            ->where('tbl.dateTimeUtc IS NULL');

        $results = $qb->getQuery()->getResult();
        foreach ($results as $result) {
            if (!$result->specialDay) {
                $this->getEntityManager()->remove($result);
                continue;
            }

            $time = str_pad((string) $result->timeInt, 4, '0', STR_PAD_LEFT);
            $date = str_pad((string) $result->specialDay->dateInt, 8, '0', STR_PAD_LEFT);

            $dateTime = DateTimeImmutable::createFromFormat(
                'dmY-Hi',
                $date. '-' . $time,
                new \DateTimeZone('Europe/London')
            );
            $dateTime = $dateTime->setTimezone(new \DateTimeZone('UTC'));

            $result->dateTimeUtc = $dateTime;
            $result->dateUtc = $dateTime;
            $this->getEntityManager()->persist($result);
        }
        $this->getEntityManager()->flush();
    }
}
