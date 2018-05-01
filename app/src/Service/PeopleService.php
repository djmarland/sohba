<?php
declare(strict_types=1);

namespace App\Service;

use App\Domain\Entity\Programme;

class PeopleService extends AbstractService
{
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

}
