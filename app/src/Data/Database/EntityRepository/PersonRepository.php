<?php
declare(strict_types=1);

namespace App\Data\Database\EntityRepository;

use Doctrine\ORM\AbstractQuery;
use Ramsey\Uuid\UuidInterface;

class PersonRepository extends AbstractEntityRepository
{
    public function getByIdWithImage(
        UuidInterface $uuid,
        int $resultType = AbstractQuery::HYDRATE_ARRAY
    ): mixed
    {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl', 'image')
            ->leftJoin('tbl.image', 'image')
            ->where('tbl.id = :id')
            ->setParameter('id', $uuid->getBytes());
        return $qb->getQuery()->getOneOrNullResult($resultType);
    }

    public function findAll(
        int $resultType = AbstractQuery::HYDRATE_ARRAY
    ): array {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')
            ->orderBy('tbl.name', 'ASC');
        return $qb->getQuery()->getResult($resultType);
    }

    public function findAllByCommittee(
        bool $onCommittee,
        int $resultType = AbstractQuery::HYDRATE_ARRAY
    ): array {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl', 'image')
            ->leftJoin('tbl.image', 'image')
            ->where('tbl.isOnCommittee = :isOnCommittee')
            ->orderBy('tbl.committeeOrder', 'ASC')
            ->addOrderBy('tbl.name', 'ASC')
            ->setParameter('isOnCommittee', $onCommittee);
        return $qb->getQuery()->getResult($resultType);
    }
}
