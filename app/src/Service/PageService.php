<?php
declare(strict_types=1);

namespace App\Service;

use App\Domain\Entity\Page;

class PageService extends AbstractService
{
    public function findByLegacyId(int $legacyId): ?Page
    {
        return $this->mapSingle(
            $this->entityManager->getPageRepo()->findByLegacyId($legacyId),
            $this->pageMapper
        );
    }

    public function findByUrl(string $urlPath): ?Page
    {
        return $this->mapSingle(
            $this->entityManager->getPageRepo()->findByUrlPath($urlPath),
            $this->pageMapper
        );
    }

    public function findAllForNavigation(): array
    {
        return $this->mapMany(
            $this->entityManager->getPageRepo()->findAllInCategories(),
            $this->pageMapper
        );
    }
}
