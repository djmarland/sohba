<?php
declare(strict_types=1);

namespace App\Service;

use App\Data\Captcha;
use App\Data\Database\Entity\Image;
use App\Data\Database\EntityManager;
use App\Data\Database\Mapper\ConfigurableContentMapper;
use App\Data\Database\Mapper\ImageMapper;
use App\Data\Database\Mapper\MapperInterface;
use App\Data\Database\Mapper\NormalListingMapper;
use App\Data\Database\Mapper\PageCategoryMapper;
use App\Data\Database\Mapper\PageMapper;
use App\Data\Database\Mapper\PersonMapper;
use App\Data\Database\Mapper\ProgrammeMapper;
use App\Data\Database\Mapper\SpecialListingMapper;
use Doctrine\ORM\Query;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\UuidInterface;
use Swift_Mailer;

abstract class AbstractService
{
    protected const TBL = 'tbl';

    protected const DEFAULT_LIMIT = 50;
    protected const DEFAULT_PAGE = 1;

    protected $entityManager;
    protected $tokenHandler;
    protected $logger;
    protected $programmeMapper;
    protected $imageMapper;
    protected $pageMapper;
    protected $configurableContentMapper;
    protected $pageCategoryMapper;
    protected $specialBroadcastMapper;
    protected $normalBroadcastMapper;
    protected $personMapper;
    protected $mailer;
    protected $captcha;

    protected $appConfigRequestFromAddress;
    protected $appConfigRequestToAddress;
    protected $appConfigSkipCaptcha;

    public function __construct(
        EntityManager $entityManager,
        ConfigurableContentMapper $configurableContentMapper,
        PageMapper $pageMapper,
        PageCategoryMapper $pageCategoryMapper,
        PersonMapper $personMapper,
        ProgrammeMapper $programmeMapper,
        SpecialListingMapper $specialBroadcastMapper,
        NormalListingMapper $normalBroadcastMapper,
        ImageMapper $imageMapper,
        Swift_Mailer $mailer,
        Captcha $captcha,
        LoggerInterface $logger,
        string $appConfigRequestFromAddress,
        string $appConfigRequestToAddress,
        bool $appConfigSkipCaptcha
    ) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->configurableContentMapper = $configurableContentMapper;
        $this->programmeMapper = $programmeMapper;
        $this->imageMapper = $imageMapper;
        $this->pageMapper = $pageMapper;
        $this->specialBroadcastMapper = $specialBroadcastMapper;
        $this->normalBroadcastMapper = $normalBroadcastMapper;
        $this->personMapper = $personMapper;
        $this->mailer = $mailer;
        $this->captcha = $captcha;
        $this->appConfigRequestFromAddress = $appConfigRequestFromAddress;
        $this->appConfigRequestToAddress = $appConfigRequestToAddress;
        $this->appConfigSkipCaptcha = $appConfigSkipCaptcha;
        $this->pageCategoryMapper = $pageCategoryMapper;
    }

    protected function mapSingle(?array $result, MapperInterface $mapper)
    {
        if ($result) {
            return $mapper->map($result);
        }
        return null;
    }

    protected function mapMany(array $results, MapperInterface $mapper): array
    {
        return array_map(
            function ($result) use ($mapper) {
                return $mapper->map($result);
            },
            $results
        );
    }

    protected function getAssociatedImageEntity($imageId): ?Image // todo - add typehint for UUID
    {
        if ($imageId === null) {
            return null;
        }

        if ($imageId instanceof UuidInterface) {
            $image = $this->entityManager->getImageRepo()->getByID(
                $imageId,
                Query::HYDRATE_OBJECT
            );
        } else {
            // todo - remove this bit
            $image = $this->entityManager->getImageRepo()->findByLegacyId(
                $imageId,
                Query::HYDRATE_OBJECT
            );
        }

        if ($image) {
            return $image;
        }
        throw new \InvalidArgumentException('Tried to use an image that does not exist');
    }
}
