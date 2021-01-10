<?php
declare(strict_types=1);

namespace App\Presenter;

use App\Domain\Entity\Person;
use App\Domain\Entity\Programme;

class PersonPresenter
{
    /**
     * @var Programme[]
     */
    private array $programmes;
    /**
     * @var Person
     */
    private Person $person;

    public function __construct(Person $person, array $showsByPersonId)
    {
        $this->programmes = $showsByPersonId[(string)$person->getId()] ?? [];
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
