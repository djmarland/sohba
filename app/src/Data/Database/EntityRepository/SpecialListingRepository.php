<?php
declare(strict_types=1);

namespace App\Data\Database\EntityRepository;

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
}
