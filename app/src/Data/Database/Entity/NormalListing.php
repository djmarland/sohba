<?php
declare(strict_types=1);

namespace App\Data\Database\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Data\Database\EntityRepository\NormalListingRepository")
 * @ORM\Table(
 *     name="tblNormalListings",
 *     indexes={
 *       @ORM\Index(name="nl_uuid", columns={"id"})
 *     },
 *     options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"},
 * )
 */
class NormalListing extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="nlistings_PKID")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public ?int $pkid = null;

    /**
     * @ORM\Column(type="integer", name="nlistings_day")
     */
    public int $day;

    /**
     * @ORM\Column(type="time_immutable", nullable=true)
     */
    public DateTimeImmutable $time;

    /**
     * @ORM\ManyToOne(targetEntity="Programme")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE", name="nlistings_show", referencedColumnName="showsPKID")
     */
    public Programme $programme;

    public function __construct(
        int $day,
        DateTimeImmutable $time,
        Programme $programme
    ) {
        parent::__construct();
        $this->day = $day;
        $this->time = $time;
        $this->programme = $programme;
    }
}
