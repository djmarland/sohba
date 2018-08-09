<?php
declare(strict_types=1);

namespace App\Domain\ValueObject;

class Time implements \JsonSerializable
{
    private $hours;
    private $minutes;

    public function __construct(
        \DateTimeImmutable $dateTime
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

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->__toString();
    }
}
