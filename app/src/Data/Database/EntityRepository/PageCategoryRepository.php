<?php
declare(strict_types=1);

namespace App\Data\Database\EntityRepository;

use Doctrine\ORM\Query;

class PageCategoryRepository extends AbstractEntityRepository
{
    public function findAllOrdered(
        $resultType = Query::HYDRATE_ARRAY
    ) {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')
            ->orderBy('tbl.order', 'ASC');
        return $qb->getQuery()->getResult($resultType);
    }
}
