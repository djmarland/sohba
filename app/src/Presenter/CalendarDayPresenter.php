<?php
declare(strict_types=1);

namespace App\Presenter;

use DateTimeImmutable;

class CalendarDayPresenter
{
    private DateTimeImmutable $date;
    private bool $isSpecial;

    public function __construct(DateTimeImmutable $date, bool $isSpecial)
    {
        $this->date = $date;
        $this->isSpecial = $isSpecial;
    }

    public function isSpecial(): bool
    {
        return $this->isSpecial;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getLink(): string
    {
        return '/schedules/' . $this->date->format('Y-m-d');
    }

    public function getDateNumber(): string
    {
        return $this->date->format('j');
    }
}
