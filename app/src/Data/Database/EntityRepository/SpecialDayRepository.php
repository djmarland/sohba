<?php
declare(strict_types=1);

namespace App\Data\Database\EntityRepository;

use App\Data\Database\Entity\SpecialDay;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Query;

class SpecialDayRepository extends AbstractEntityRepository
{
    public function findForDate(
        DateTimeInterface $dateTime,
        $resultType = Query::HYDRATE_ARRAY
    ) {
        // todo - store a real date object in the database rather than an int
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')
            ->where('tbl.dateInt = :value')
            ->setParameter('value', $this->dateToLegacyInt($dateTime));
        return $qb->getQuery()->getOneOrNullResult($resultType);
    }

    public function findAllAfterDate(
        DateTimeInterface $dateTime,
        $resultType = Query::HYDRATE_ARRAY
    ) {
        // todo - store a real date object in the database rather than using timestamp
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')
            ->where('tbl.timestamp > :timestamp')
            ->orderBy('tbl.timestamp', 'ASC')
            ->setParameter('timestamp', $dateTime->getTimestamp());
        return $qb->getQuery()->getResult($resultType);
    }

    public function findAllBetweenDate(
        DateTimeInterface $dateTime,
        $resultType = Query::HYDRATE_ARRAY
    ) {
        // todo - store a real date object in the database rather than using timestamp
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')
            ->where('tbl.timestamp > :timestamp')
            ->orderBy('tbl.timestamp', 'ASC')
            ->setParameter('timestamp', $dateTime->getTimestamp());
        return $qb->getQuery()->getResult($resultType);
    }

    public function deleteBetween(DateTimeImmutable $fromInclusive, DateTimeImmutable $toExclusive): void
    {
        $sql = 'DELETE FROM ' . SpecialDay::class . ' t WHERE t.timestamp >= :from AND t.timestamp < :to';
        $query = $this->getEntityManager()
            ->createQuery($sql)
            ->setParameter('from', $fromInclusive->getTimestamp())
            ->setParameter('to', $toExclusive->getTimestamp());
        $query->execute();
    }

    private function dateToLegacyInt(DateTimeInterface $date): int
    {
        return (int)$date->format('jmY');
    }
}
