<?php
declare(strict_types=1);

namespace App\Service;

use App\Data\Database\Entity\Person as DbPerson;
use App\Data\ID;
use App\Domain\Entity\Person;
use App\Domain\Entity\Programme;

class PeopleService extends AbstractService
{
    public function findAll(): array
    {
        return $this->mapMany(
            $this->entityManager->getPersonRepo()->findAll(),
            $this->personMapper
        );
    }

    public function findExecutiveCommittee(): array
    {
        return $this->mapMany(
            $this->entityManager->getPersonRepo()->findAllByCommittee(true),
            $this->personMapper
        );
    }

    public function findOtherMembers(): array
    {
        return $this->mapMany(
            $this->entityManager->getPersonRepo()->findAllByCommittee(false),
            $this->personMapper
        );
    }

    public function findForProgramme(Programme $programme): array
    {
        $peopleInProgramme = $this->entityManager->getPersonInShowRepo()
            ->findPeopleForProgrammeId($programme->getLegacyId());

        $people = array_map(function (array $personInProgramme) {
            return $personInProgramme['person'];
        }, $peopleInProgramme);

        return $this->mapMany(
            $people,
            $this->personMapper
        );
    }

    public function newPerson(string $name): int
    {
        $page = new DbPerson(
            ID::makeNewID(DbPerson::class),
            $name
        );

        $this->entityManager->persist($page);
        $this->entityManager->flush();

        return $page->pkid;
    }

    public function deletePerson(int $programmeId): void
    {
        $this->entityManager->getPersonRepo()->deleteByLegacyId($programmeId);
    }

    public function findByLegacyId($legacyId): ?Person
    {
        return $this->mapSingle(
            $this->entityManager->getPersonRepo()->findByLegacyId($legacyId),
            $this->personMapper
        );
    }

    public function updatePerson(
        Person $page,
        string $name,
        bool $onExec,
        ?string $committeeTitle,
        ?int $committeePosition,
        ?int $imageId
    ): void {
        $this->entityManager->getPersonRepo()->updatePerson(
            $page->getLegacyId(),
            $name,
            $onExec,
            $committeeTitle,
            $committeePosition,
            $imageId
        );
    }
}
