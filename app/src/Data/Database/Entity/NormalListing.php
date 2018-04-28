<?php
declare(strict_types=1);

namespace App\Data\Database\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Data\Database\EntityRepository\NormalListingRepository")
 * @ORM\Table(
 *     name="tblNormalListings",
 *     options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"},
 * )
 */
class NormalListing extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="nlistings_PKID")
     */
    public $pkid;

    /**
     * @ORM\Column(type="integer", name="nlistings_day")
     */
    public $day;

    /**
     * @ORM\Column(type="integer", name="nlistings_startTime")
     */
    public $timeInt;

    /**
     * @ORM\ManyToOne(targetEntity="Programme")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE", name="nlistings_show", referencedColumnName="showsPKID")
     */
    public $programme;

    public function __construct(
        UuidInterface $id,
        int $timeInt
    ) {
        parent::__construct($id);
        $this->timeInt = $timeInt;
    }
}
