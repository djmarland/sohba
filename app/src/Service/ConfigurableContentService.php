<?php
declare(strict_types=1);

namespace App\Service;

use App\Data\Database\Entity\KeyValue;
use App\Domain\Entity\ConfigurableContent;
use Doctrine\ORM\AbstractQuery;
use InvalidArgumentException;
use Ramsey\Uuid\UuidInterface;

class ConfigurableContentService extends AbstractService
{
    public const KEY_CRICKET_STREAM_URL = 'CRICKET_STREAM_URL';
    public const KEY_LIVE_STREAM_URL = 'LIVE_STREAM_URL';
    public const KEY_FACEBOOK_INTRO_TEXT = 'FACEBOOK_INTRO_TEXT';
    public const KEY_FACEBOOK_URL = 'FACEBOOK_URL';
    public const KEY_FOOTER_CONTENT = 'FOOTER_CONTENT';
    public const KEY_INSTAGRAM_INTRO_TEXT = 'INSTAGRAM_INTRO_TEXT';
    public const KEY_INSTAGRAM_URL = 'INSTAGRAM_URL';
    public const KEY_PHONE_NUMBER = 'PHONE_NUMBER';
    public const KEY_REQUESTLINE_INTRO = 'REQUESTLINE_INTRO';
    public const KEY_TWITTER_INTRO_TEXT = 'TWITTER_INTRO_TEXT';
    public const KEY_TWITTER_URL = 'TWITTER_URL';
    public const KEY_WEBSITE_TITLE = 'WEBSITE_TITLE';
    public const KEY_X_TECHNICAL_DETAILS = 'X_TECHNICAL_DETAILS';

    // true means it is richText
    private const ALL_KEYS = [
        self::KEY_CRICKET_STREAM_URL => false,
        self::KEY_LIVE_STREAM_URL => false,
        self::KEY_FOOTER_CONTENT => true,
        self::KEY_FACEBOOK_INTRO_TEXT => true,
        self::KEY_FACEBOOK_URL => false,
        self::KEY_INSTAGRAM_INTRO_TEXT => true,
        self::KEY_INSTAGRAM_URL => false,
        self::KEY_REQUESTLINE_INTRO => true,
        self::KEY_PHONE_NUMBER => false,
        self::KEY_TWITTER_INTRO_TEXT => true,
        self::KEY_TWITTER_URL => false,
        self::KEY_WEBSITE_TITLE => false,
        self::KEY_X_TECHNICAL_DETAILS => true,
    ];

    private ?array $allCache = null;

    public function findByUuid(UuidInterface $uuid): ?ConfigurableContent
    {
        return $this->mapSingle(
            $this->entityManager->getKeyValueRepo()->getByID($uuid),
            $this->configurableContentMapper
        );
    }

    public function getValue(string $key): ?ConfigurableContent
    {
        /** @var ConfigurableContent | null $v */
        $v = $this->getAll()[$key] ?? null;
        if ($v && $v->hasValue()) {
            return $v;
        }
        return null;
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
        /** @var KeyValue|null $entity */
        $entity = $this->entityManager->getKeyValueRepo()->getByID(
            $content->getId(),
            AbstractQuery::HYDRATE_OBJECT
        );
        if (!$entity) {
            throw new InvalidArgumentException('Tried to update a value that does not exist');
        }

        $entity->description = $description;
        $entity->value = $value;

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }
}
