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
use Doctrine\ORM\AbstractQuery;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Mailer\MailerInterface;

abstract class AbstractService
{
    protected string $appConfigRequestFromAddress;
    protected string $appConfigRequestToAddress;
    protected bool $appConfigSkipCaptcha;

    public function __construct(
        protected EntityManager $entityManager,
        protected ConfigurableContentMapper $configurableContentMapper,
        protected PageMapper $pageMapper,
        protected PageCategoryMapper $pageCategoryMapper,
        protected PersonMapper $personMapper,
        protected ProgrammeMapper $programmeMapper,
        protected SpecialListingMapper $specialBroadcastMapper,
        protected NormalListingMapper $normalBroadcastMapper,
        protected ImageMapper $imageMapper,
        protected MailerInterface $mailer,
        protected Captcha $captcha,
        protected LoggerInterface $logger,
        string $appConfigRequestFromAddress,
        string $appConfigRequestToAddress,
        bool $appConfigSkipCaptcha
    ) {
        $this->appConfigRequestFromAddress = $appConfigRequestFromAddress;
        $this->appConfigRequestToAddress = $appConfigRequestToAddress;
        $this->appConfigSkipCaptcha = $appConfigSkipCaptcha;
    }

    protected function mapSingle(?array $result, MapperInterface $mapper): mixed
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

    protected function getAssociatedImageEntity(?UuidInterface $imageId): ?Image
    {
        if ($imageId === null) {
            return null;
        }

        $image = $this->entityManager->getImageRepo()->getByID(
            $imageId,
            AbstractQuery::HYDRATE_OBJECT
        );

        if ($image) {
            return $image;
        }
        throw new InvalidArgumentException('Tried to use an image that does not exist');
    }
}
