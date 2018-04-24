<?php
declare(strict_types=1);

namespace App\Data\Database\EntityRepository;

use Doctrine\ORM\Query;

class ProgrammeRepository extends AbstractEntityRepository
{
    public function findAllActive(
        $resultType = Query::HYDRATE_ARRAY
    ) {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')
            // todo - add the WHERE clause to find active
            ->orderBy('tbl.title', 'ASC');
        return $qb->getQuery()->getResult($resultType);
    }
}
