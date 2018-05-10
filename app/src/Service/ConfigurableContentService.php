<?php
declare(strict_types=1);

namespace App\Service;

use App\Data\Database\Entity\KeyValue;
use App\Data\ID;

class ConfigurableContentService extends AbstractService
{
    private const KEY_FOOTER_CONTENT = 'FOOTER_CONTENT';

    public function getFooterContent(): string
    {
        $result = $this->entityManager->getKeyValueRepo()
            ->findValueByKey(self::KEY_FOOTER_CONTENT);

        return $result;
    }

    public function addKeyValue($key, $value)
    {
        $kv = new KeyValue(ID::makeNewID(KeyValue::class), $key, $value);
        $this->entityManager->persist($kv);
        $this->entityManager->flush();
    }
}
