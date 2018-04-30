<?php
declare(strict_types=1);

namespace App\Data\Database\EntityRepository;

use Doctrine\ORM\Query;

class PersonRepository extends AbstractEntityRepository
{
    public function findAllByCommittee(
        bool $onCommittee,
        $resultType = Query::HYDRATE_ARRAY
    ) {
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
