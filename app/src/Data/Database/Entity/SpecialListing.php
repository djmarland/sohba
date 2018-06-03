<?php
declare(strict_types=1);

namespace App\Data\Database\Entity;

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
     * @ORM\Column(type="string", name="spdNote", length=500, nullable=true)
     */
    public $internalNote;

    /**
     * @ORM\Column(type="string", name="spdPublicNote", length=500, nullable=true)
     */
    public $publicNote;

    /**
     * @ORM\ManyToOne(targetEntity="SpecialDay")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE", name="spdDateID", referencedColumnName="sdPKID")
     */
    public $specialDay;

    /**
     * @ORM\ManyToOne(targetEntity="Programme")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE", name="spdShow", referencedColumnName="showsPKID")
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
