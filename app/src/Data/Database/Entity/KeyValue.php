<?php
declare(strict_types=1);

namespace App\Data\Database\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Data\Database\EntityRepository\KeyValueRepository")
 * @ORM\Table(
 *     name="keyValues",
 *     indexes={@ORM\Index(name="key_value_key", columns={"contentKey"})},
 *     options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"},
 * )
 */
class KeyValue extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid_binary")
     */
    public $id;

    /**
     * @ORM\Column(type="string", name="contentKey", length=191, unique=true)
     */
    public $key;

    /**
     * @ORM\Column(type="text")
     */
    public $description;

    /**
     * @ORM\Column(type="text")
     */
    public $value;

    /**
     * @ORM\Column(type="boolean")
     */
    public $isRichText = false;

    public function __construct(
        string $key,
        string $value,
        string $description
    ) {
        parent::__construct();
        $this->key = $key;
        $this->value = $value;
        $this->description = $description;
    }
}
