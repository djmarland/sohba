<?php
declare(strict_types=1);

namespace App\Data\Database\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Data\Database\EntityRepository\SpecialListingRepository")
 * @ORM\Table(
 *     name="tblSpecialListings",
 *     options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"},
 * )
 */
class SpecialListing extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="spdPKID")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $pkid;

    /**
     * @ORM\Column(type="integer", name="spdStartTime")
     */
    public $timeInt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    public $dateTimeUk;

    /**
     * @ORM\Column(type="date_immutable", nullable=true)
     */
    public $dateUk;

    /**
     * @ORM\Column(type="string", name="spdNote", length=500, nullable=true)
     */
    public $internalNote;

    /**
     * @ORM\Column(type="string", name="spdPublicNote", length=500, nullable=true)
     */
    public $publicNote;

    /**
     * @ORM\ManyToOne(targetEntity="SpecialDay")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE", name="spdDateID", referencedColumnName="sdPKID")
     */
    public $specialDay;

    /**
     * @ORM\ManyToOne(targetEntity="Programme")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE", name="spdShow", referencedColumnName="showsPKID")
     */
    public $programme;

    public function __construct(
        UuidInterface $id,
        DateTimeImmutable $dateTime,
        Programme $programme
    ) {
        parent::__construct($id);
        $this->dateTimeUk = $dateTime;
        $this->dateUk = $dateTime;

        $timeInt = (int)$dateTime->format('Hi'); // todo - remove

        $this->timeInt = $timeInt;
        $this->programme = $programme;
    }
}
