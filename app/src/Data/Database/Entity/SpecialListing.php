<?php
declare(strict_types=1);

namespace App\Data\Database\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Data\Database\EntityRepository\SpecialListingRepository")
 * @ORM\Table(
 *     name="tblSpecialListings",
 *     indexes={
 *       @ORM\Index(name="sl_uuid", columns={"id"})
 *     },
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
    public ?int $pkid = null;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    public ?DateTimeImmutable $dateTimeUk = null;

    /**
     * @ORM\Column(type="date_immutable", nullable=true)
     */
    public ?DateTimeImmutable $dateUk = null;

    /**
     * @ORM\Column(type="string", name="spdNote", length=500, nullable=true)
     */
    public ?string $internalNote = null;

    /**
     * @ORM\Column(type="string", name="spdPublicNote", length=500, nullable=true)
     */
    public ?string $publicNote = null;

    /**
     * @ORM\ManyToOne(targetEntity="Programme")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE", name="spdShow", referencedColumnName="showsPKID")
     */
    public Programme $programme;

    public function __construct(
        DateTimeImmutable $dateTime,
        Programme $programme
    ) {
        parent::__construct();
        $this->dateTimeUk = $dateTime;
        $this->dateUk = $dateTime;
        $this->programme = $programme;
    }
}
