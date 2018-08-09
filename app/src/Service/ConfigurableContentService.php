<?php
declare(strict_types=1);

namespace App\Service;

use App\Data\Database\Entity\KeyValue;
use App\Domain\Entity\ConfigurableContent;
use Doctrine\ORM\Query;
use Ramsey\Uuid\UuidInterface;

class ConfigurableContentService extends AbstractService
{
    public const KEY_CRICKET_STREAM_URL = 'CRICKET_STREAM_URL';
    public const KEY_FACEBOOK_INTRO_TEXT = 'FACEBOOK_INTRO_TEXT';
    public const KEY_FACEBOOK_URL = 'FACEBOOK_URL';
    public const KEY_FOOTER_CONTENT = 'FOOTER_CONTENT';
    public const KEY_PHONE_NUMBER = 'PHONE_NUMBER';
    public const KEY_REQUESTLINE_INTRO = 'REQUESTLINE_INTRO';
    public const KEY_TWITTER_INTRO_TEXT = 'TWITTER_INTRO_TEXT';
    public const KEY_TWITTER_URL = 'TWITTER_URL';
    public const KEY_WEBSITE_TITLE = 'WEBSITE_TITLE';
    public const KEY_X_TECHNICAL_DETAILS = 'X_TECHNICAL_DETAILS';

    // true means it is richText
    private const ALL_KEYS = [
        self::KEY_CRICKET_STREAM_URL => false,
        self::KEY_FOOTER_CONTENT => true,
        self::KEY_FACEBOOK_INTRO_TEXT => true,
        self::KEY_FACEBOOK_URL => false,
        self::KEY_REQUESTLINE_INTRO => true,
        self::KEY_PHONE_NUMBER => false,
        self::KEY_TWITTER_INTRO_TEXT => true,
        self::KEY_TWITTER_URL => false,
        self::KEY_WEBSITE_TITLE => false,
        self::KEY_X_TECHNICAL_DETAILS => true,
    ];

    private $allCache;

    public function findByUuid(UuidInterface $uuid)
    {
        return $this->mapSingle(
            $this->entityManager->getKeyValueRepo()->getByID($uuid),
            $this->configurableContentMapper
        );
    }

    public function getValue(string $key)
    {
        if (!\array_key_exists($key, self::ALL_KEYS)) {
            throw new \InvalidArgumentException($key . ' is not a valid key');
        }

        return $this->getAll()[$key];
    }

    public function ensureKeysExist(): void
    {
        $existing = array_keys($this->getAll());
        $expectedKeys = array_keys(self::ALL_KEYS);
        $missingKeys = array_diff($expectedKeys, $existing);
        $extraKeys = array_diff($existing, $expectedKeys);

        if (empty($missingKeys) && empty($extraKeys)) {
            return;
        }

        if (!empty($missingKeys)) {
            foreach ($missingKeys as $key) {
                $kv = new KeyValue($key, '', '');
                $kv->isRichText = self::ALL_KEYS[$key];
                $this->entityManager->persist($kv);
            }
            $this->entityManager->flush();
        }

        // don't delete keys right now, as we want to upload them in advance
        //if (!empty($extraKeys)) {
        //    $this->entityManager->getKeyValueRepo()->deleteKeys($extraKeys);
        //}

        $this->allCache = null;
    }

    public function getAll(): array
    {
        if ($this->allCache) {
            return $this->allCache;
        }

        $all = $this->mapMany(
            $this->entityManager->getKeyValueRepo()->findAll(),
            $this->configurableContentMapper
        );

        $this->allCache = [];
        foreach ($all as $content) {
            /** @var ConfigurableContent $content */
            $this->allCache[$content->getKey()] = $content;
        }

        return $this->allCache;
    }

    public function updateEntry(
        ConfigurableContent $content,
        string $description,
        string $value
    ): void {
        /** @var KeyValue $entity */
        $entity = $this->entityManager->getKeyValueRepo()->getByID(
            $content->getId(),
            Query::HYDRATE_OBJECT
        );
        if (!$entity) {
            throw new \InvalidArgumentException('Tried to update a page that does not exist');
        }

        $entity->description = $description;
        $entity->value = $value;

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }
}
