<?php
declare(strict_types=1);

namespace App\Domain\ValueObject;

use DateTimeImmutable;
use JsonSerializable;
use function str_pad;

class Time implements JsonSerializable
{
    private int $hours;
    private int $minutes;

    public function __construct(
        DateTimeImmutable $dateTime
    ) {
        $this->hours = (int)$dateTime->format('H');
        $this->minutes = (int)$dateTime->format('i');
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

    public function isBeforeOrAt(self $compare): bool
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

    public function __toString(): string
    {
        return $this->getFormatted();
    }

    private function pad(string $str): string
    {
        return str_pad($str, 2, '0', STR_PAD_LEFT);
    }

    public function jsonSerialize(): string
    {
        return $this->__toString();
    }
}
