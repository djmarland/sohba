<?php
declare(strict_types=1);

namespace App\Domain\ValueObject;

class Time
{
    private $hours;
    private $minutes;

    public function __construct(
        int $hours,
        int $minutes
    ) {
        if ($hours < 0 ||
            $minutes < 0 ||
            $hours > 23 ||
            $minutes > 59
        ) {
            throw new \InvalidArgumentException(
                'Hours: ' . $hours . ', Mins: ' . $minutes . ' is an invalid Time'
            );
        }
        $this->hours = $hours;
        $this->minutes = $minutes;
    }

    public function getHours(): int
    {
        return $this->hours;
    }

    public function getMinutes(): int
    {
        return $this->minutes;
    }

    public function getFormatted(): string
    {
        return $this->pad((string)$this->hours) . ':' . $this->pad((string)$this->minutes);
    }

    public function isBeforeOrAt(self $compare)
    {
        if ($this->hours < $compare->getHours()) {
            return true;
        }
        if ($this->hours > $compare->getHours()) {
            return false;
        }

        // same hour
        return ($this->minutes <= $compare->getMinutes());
    }

    public function __toString()
    {
        return $this->getFormatted();
    }

    private function pad(string $str): string
    {
        return \str_pad($str, 2, '0', STR_PAD_LEFT);
    }
}
