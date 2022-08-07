<?php
declare(strict_types=1);

namespace App\Data\Database\EntityRepository;

use App\Data\Database\Entity\Programme;
use App\Data\Database\Entity\SpecialListing;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\AbstractQuery;

class SpecialListingRepository extends AbstractEntityRepository
{
    public function findListingsOfTypesAfter(
        array $programmeTypes,
        DateTimeImmutable $after,
        int $limit = null,
        int $resultType = AbstractQuery::HYDRATE_ARRAY
    ): array {
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
        int $resultType = AbstractQuery::HYDRATE_ARRAY
    ): array {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl', 'programme', 'image')
            ->where('tbl.dateUk = :dateUk')
            ->innerJoin('tbl.programme', 'programme')
            ->leftJoin('programme.image', 'image')
            ->orderBy('tbl.dateTimeUk', 'ASC')
            ->setParameter('dateUk', $specialDate);
        return $qb->getQuery()->getResult($resultType);
    }

    /**
     * @return mixed
     */
    public function findNextForProgramme(
        Programme $programme,
        DateTimeImmutable $now,
        int $resultType = AbstractQuery::HYDRATE_ARRAY
    ) {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')
            ->where('tbl.dateUk >= :after')
            ->andWhere('tbl.programme = :programme')
            ->setMaxResults(1)
            ->setParameter('after', $now)
            ->setParameter('programme', $programme);

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

        return array_map(static fn ($r) => reset($r), $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY));
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
