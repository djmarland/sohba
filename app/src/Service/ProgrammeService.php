<?php
declare(strict_types=1);

namespace App\Service;

class ProgrammeService extends AbstractService
{
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
