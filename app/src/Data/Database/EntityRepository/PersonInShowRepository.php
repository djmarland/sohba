<?php
declare(strict_types=1);

namespace App\Data\Database\EntityRepository;

use Doctrine\ORM\Query;

class PersonInShowRepository extends AbstractEntityRepository
{
    public function findAll(
        $personIds = [],
        $resultType = Query::HYDRATE_ARRAY
    ) {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl', 'programme', 'person')
            ->innerJoin('tbl.programme', 'programme')
            ->innerJoin('tbl.person', 'person')
            ->orderBy('programme.title', 'ASC');

        if (!empty($personIds)) {
            $qb->where('tbl.person IN (:personIds)');
            $qb->setParameter('personIds', $personIds);
        }

        return $qb->getQuery()->getResult($resultType);
    }
}
