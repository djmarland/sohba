<?php
declare(strict_types=1);

namespace App\Data\Database\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Data\Database\EntityRepository\SpecialDayRepository")
 * @ORM\Table(
 *     name="tblSpecialDays",
 *     options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"},
 * )
 */
class SpecialDay extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="sdPKID")
     */
    public $pkid;

    /**
     * @ORM\Column(type="integer", name="sdDate")
     */
    public $dateInt;

    /**
     * @ORM\Column(type="integer", name="sdStamp")
     */
    public $timestamp;

    /**
     * @ORM\Column(type="string", name="sdNote", length=500, nullable=true)
     */
    public $internalNote;

    /**
     * @ORM\Column(type="string", name="sdPublicNote", length=500, nullable=true)
     */
    public $publicNote;

    public function __construct(
        UuidInterface $id,
        int $dateInt
    ) {
        parent::__construct($id);
        $this->dateInt = $dateInt;
    }
}
