<?php
declare(strict_types=1);

namespace App\Data\Database\EntityRepository;

use Doctrine\ORM\AbstractQuery;

class PageCategoryRepository extends AbstractEntityRepository
{
    public function findAllOrdered(
        int $resultType = AbstractQuery::HYDRATE_ARRAY
    ): array {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')
            ->orderBy('tbl.order', 'ASC');
        return $qb->getQuery()->getResult($resultType);
    }
}
