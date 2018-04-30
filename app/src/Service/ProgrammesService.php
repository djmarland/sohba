<?php
declare(strict_types=1);

namespace App\Service;

use App\Domain\Entity\Programme;

class ProgrammesService extends AbstractService
{
    public function findByLegacyId(int $id): ?Programme
    {
        return $this->mapSingle(
            $this->entityManager->getProgrammeRepo()->findByLegacyId($id),
            $this->programmeMapper
        );
    }

    public function getAllActive(): array
    {
        return $this->mapMany(
            $this->entityManager->getProgrammeRepo()->findAllActive(),
            $this->programmeMapper
        );
    }

    public function getAllByPersonIds(array $ids = []): array
    {
        $results = $this->entityManager->getPersonInShowRepo()->findAll($ids);
        $groupedResults = [];

        foreach ($results as $result) {
            $personId = $result['person']['pkid'];
            if (!isset($groupedResults[$personId])) {
                $groupedResults[$personId] = [];
            }
            $groupedResults[$personId][] = $this->mapSingle($result['programme'], $this->programmeMapper);
        }

        return $groupedResults;
    }
}
