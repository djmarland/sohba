<?php
declare(strict_types=1);

namespace App\Domain\Entity\Null;

use App\Domain\Entity\PageCategory;
use Ramsey\Uuid\Uuid;

class NullPageCategory extends PageCategory
{
    use NullBaseTrait;

    public function __construct()
    {
        parent::__construct(
            Uuid::fromString(Uuid::NIL),
            ''
        );
    }
}
