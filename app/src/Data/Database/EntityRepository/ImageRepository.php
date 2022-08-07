<?php
declare(strict_types=1);

namespace App\Data\Database\EntityRepository;

use Doctrine\ORM\AbstractQuery;

class ImageRepository extends AbstractEntityRepository
{
    public function findAll(
        int $resultType = AbstractQuery::HYDRATE_ARRAY
    ): array {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')
            ->orderBy('tbl.pkid', 'DESC');
        return $qb->getQuery()->getResult($resultType);
    }
}
