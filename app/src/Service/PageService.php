<?php
declare(strict_types=1);

namespace App\Service;

use App\Domain\Entity\Page;

class PageService extends AbstractService
{
    public function findByLegacyId(int $legacyId): ?Page
    {
        $result = $this->entityManager->getPageRepo()
            ->findByLegacyId($legacyId);
        if ($result) {
            return $this->pageMapper->map($result);
        }
        return null;
    }

    public function findAllForNavigation(): array
    {
        $results = $this->entityManager->getPageRepo()
            ->findAllInCategories();

        return array_map(
            function ($result) {
                return $this->pageMapper->map($result);
            },
            $results
        );
    }
}
