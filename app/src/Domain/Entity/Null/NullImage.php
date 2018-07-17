<?php
declare(strict_types=1);

namespace App\Domain\Entity\Null;

use App\Domain\Entity\Image;
use Ramsey\Uuid\Uuid;

class NullImage extends Image
{
    use NullBaseTrait;

    public function __construct()
    {
        parent::__construct(
            Uuid::fromString(Uuid::NIL),
            0,
            ''
        );
    }
}
