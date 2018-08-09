<?php
declare(strict_types=1);

namespace App\Data\Database\EntityRepository;

use App\Data\Database\Entity\SpecialListing;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Query;

class SpecialListingRepository extends AbstractEntityRepository
{
    public function findListingsOfTypesAfter(
        array $programmeTypes,
        DateTimeImmutable $after,
        int $limit = null,
        $resultType = Query::HYDRATE_ARRAY
    ) {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl', 'programme')
            ->innerJoin('tbl.programme', 'programme')
            ->where('tbl.dateTimeUk >= :after')
            ->andWhere('programme.type IN (:types)')
            ->orderBy('tbl.dateTimeUk', 'ASC')
            ->setParameter('after', $after)
            ->setParameter('types', $programmeTypes);

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult($resultType);
    }

    public function findAllForDate(
        DateTimeImmutable $specialDate,
        $resultType = Query::HYDRATE_ARRAY
    ) {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl', 'programme', 'image')
            ->where('tbl.dateUk = :dateUk')
            ->innerJoin('tbl.programme', 'programme')
            ->leftJoin('programme.image', 'image')
            ->orderBy('tbl.dateTimeUk', 'ASC')
            ->setParameter('dateUk', $specialDate);
        return $qb->getQuery()->getResult($resultType);
    }

    public function findNextForLegacyProgrammeId(
        int $getLegacyId,
        DateTimeImmutable $now,
        $resultType = Query::HYDRATE_ARRAY
    ) {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')
            ->where('tbl.dateUk >= :after')
            ->andWhere('IDENTITY(tbl.programme) = :programmeId')
            ->setMaxResults(1)
            ->setParameter('after', $now)
            ->setParameter('programmeId', $getLegacyId);

        return $qb->getQuery()->getOneOrNullResult($resultType);
    }

    public function findDates(
        ?DateTimeInterface $from = null,
        ?DateTimeInterface $to = null
    ): array {
        $qb = $this->createQueryBuilder('tbl')
            ->select('DISTINCT(tbl.dateUk)')
            ->orderBy('tbl.dateUk', 'ASC');

        if ($from) {
            $qb = $qb->andWhere('tbl.dateUk >= :from')
                ->setParameter('from', $from);
        }
        if ($to) {
            $qb = $qb->andWhere('tbl.dateUk < :to')
                ->setParameter('to', $to);
        }

        return array_map('reset', $qb->getQuery()->getResult(Query::HYDRATE_ARRAY));
    }

    public function deleteBetween(DateTimeImmutable $fromInclusive, DateTimeImmutable $toExclusive): void
    {
        $sql = 'DELETE FROM ' . SpecialListing::class . ' t WHERE t.dateTimeUk >= :from AND t.dateTimeUk < :to';
        $query = $this->getEntityManager()
            ->createQuery($sql)
            ->setParameter('from', $fromInclusive)
            ->setParameter('to', $toExclusive);
        $query->execute();
    }
}
