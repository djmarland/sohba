<?php
declare(strict_types=1);

namespace App\Service;

use App\Data\Database\Entity\Programme as DbProgramme;
use App\Data\ID;
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

    public function getAll(): array
    {
        return $this->mapMany(
            $this->entityManager->getProgrammeRepo()->findAll(),
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

    public function getAllRegular(): array
    {
        return $this->mapMany(
            $this->entityManager->getProgrammeRepo()->findByTypes([
                Programme::PROGRAMME_TYPE_REGULAR
            ]),
            $this->programmeMapper
        );
    }

    public function getAllEvents(): array
    {
        return $this->mapMany(
            $this->entityManager->getProgrammeRepo()->findByTypes(array_keys(
                Programme::PROGRAMME_EVENT_TYPES
            )),
            $this->programmeMapper
        );
    }

    public function getAllByPersonIds(array $ids = [], ?Programme $exclude = null): array
    {
        $results = $this->entityManager->getPersonInShowRepo()->findAll($ids);
        $groupedResults = [];

        foreach ($results as $result) {
            if ($exclude && $exclude->getLegacyId() === $result['programme']['pkid']) {
                continue;
            }

            $personId = $result['person']['pkid'];
            if (!isset($groupedResults[$personId])) {
                $groupedResults[$personId] = [];
            }
            $groupedResults[$personId][] = $this->mapSingle($result['programme'], $this->programmeMapper);
        }

        return $groupedResults;
    }

    public function newProgramme(string $name, int $type = Programme::PROGRAMME_TYPE_REGULAR): int
    {
        $page = new DbProgramme(
            ID::makeNewID(DbProgramme::class),
            $name,
            $type
        );

        $this->entityManager->persist($page);
        $this->entityManager->flush();

        return $page->pkid;
    }

    public function deleteProgramme(int $programmeId): void
    {
        $this->entityManager->getProgrammeRepo()->deleteByLegacyId($programmeId);
    }

    public function updateProgramme(
        Programme $page,
        string $name,
        string $tagLine,
        int $type,
        string $description,
        ?int $imageId
    ): void {
        $this->entityManager->getProgrammeRepo()->updateProgramme(
            $page->getLegacyId(),
            $name,
            $tagLine,
            $type,
            $description,
            $imageId
        );
    }

    public function migrate(): void
    {
        $this->entityManager->getProgrammeRepo()->migrate(); // todo - remove
    }
}
