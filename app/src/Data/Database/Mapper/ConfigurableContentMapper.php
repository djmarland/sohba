<?php
declare(strict_types=1);

namespace App\Data\Database\Mapper;

use App\Domain\Entity\ConfigurableContent;
use App\Domain\ValueObject\RichText;

class ConfigurableContentMapper implements MapperInterface
{
    public function map(array $item): ConfigurableContent
    {
        $richContent = null;
        $simpleContent = null;

        if ($item['isRichText']) {
            $richContent = new RichText($item['value']);
        } else {
            $simpleContent = $item['value'];
        }

        return new ConfigurableContent(
            $item['id'],
            $item['key'],
            $item['description'],
            $simpleContent,
            $richContent
        );
    }
}
