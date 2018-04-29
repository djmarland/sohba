<?php
declare(strict_types=1);

namespace App\Service;

use App\Domain\Entity\Programme;

class ProgrammesService extends AbstractService
{
    public function findByLegacyId(int $id): ?Programme
    {
        $result = $this->entityManager->getProgrammeRepo()
            ->findByLegacyId($id);
        if ($result) {
            return $this->programmeMapper->map($result);
        }
        return null;
    }

    public function getAllActive(): array
    {
        $results = $this->entityManager->getProgrammeRepo()
            ->findAllActive();

        return array_map(
            function ($result) {
                return $this->programmeMapper->map($result);
            },
            $results
        );
    }
}
