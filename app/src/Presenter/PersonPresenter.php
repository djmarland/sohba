<?php
declare(strict_types=1);

namespace App\Presenter;

use App\Domain\Entity\Person;

class PersonPresenter
{
    private $programmes;
    /**
     * @var Person
     */
    private $person;

    public function __construct(Person $person, array $showsByPersonId)
    {
        $this->programmes = $showsByPersonId[$person->getLegacyId()] ?? [];
        $this->person = $person;
    }

    public function hasProgrammes(): bool
    {
        return !empty($this->programmes);
    }

    public function getProgrammes(): array
    {
        return $this->programmes;
    }

    public function getPerson(): Person
    {
        return $this->person;
    }
}
