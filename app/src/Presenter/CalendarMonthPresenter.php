<?php
declare(strict_types=1);

namespace App\Presenter;

use DateInterval;
use DateTimeImmutable;

use function App\Functions\DateTimes\formatMonthForDisplay;

class CalendarMonthPresenter
{
    private $weeks = [];
    private $startOfMonth;

    public function __construct(DateTimeImmutable $startOfMonth, array $specialDayFlags)
    {
        $this->startOfMonth = $startOfMonth;
        $this->weeks = $this->calculate($startOfMonth, $specialDayFlags);
    }

    public function getWeeks(): array
    {
        return $this->weeks;
    }

    public function getMonthTitle(): string
    {
        return formatMonthForDisplay($this->startOfMonth);
    }

    private function calculate(DateTimeImmutable $startOfMonth, array $specialDayFlags): array
    {
        $weeks = [];

        // first rewind to monday
        $firstDayOffset = (int)$startOfMonth->format('N') - 1;
        $startOfNextMonth = $startOfMonth->add(new DateInterval('P1M'));

        $dateIncrement = $startOfMonth->sub(new DateInterval('P' . $firstDayOffset . 'D'));

        while ($dateIncrement < $startOfNextMonth) {
            $week = [];

            for ($i = 0; $i < 7; $i++) {
                $day = null;
                if ($dateIncrement >= $startOfMonth &&
                    $dateIncrement < $startOfNextMonth) {
                    $day = new CalendarDayPresenter(
                        $dateIncrement,
                        isset($specialDayFlags[$dateIncrement->format('Y-m-d')])
                    );
                }
                $week[] = $day;
                $dateIncrement = $dateIncrement->add(new DateInterval('P1D'));
            }
            $weeks[] = $week;
        }
        return $weeks;
    }
}
