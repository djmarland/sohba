<?php
declare(strict_types=1);

namespace App\Data\Database\EntityRepository;

use Doctrine\ORM\Query;

class ImageRepository extends AbstractEntityRepository
{
    public function findAll(
        int $resultType = Query::HYDRATE_ARRAY
    ) {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')
            ->orderBy('tbl.pkid', 'DESC');
        return $qb->getQuery()->getResult($resultType);
    }
}
