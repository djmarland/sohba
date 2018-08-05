<?php
declare(strict_types=1);

namespace App\Service;

use App\Data\Captcha;
use App\Data\Database\EntityManager;
use App\Data\Database\Mapper\ImageMapper;
use App\Data\Database\Mapper\MapperInterface;
use App\Data\Database\Mapper\NormalListingMapper;
use App\Data\Database\Mapper\PageCategoryMapper;
use App\Data\Database\Mapper\PageMapper;
use App\Data\Database\Mapper\PersonMapper;
use App\Data\Database\Mapper\ProgrammeMapper;
use App\Data\Database\Mapper\SpecialDayMapper;
use App\Data\Database\Mapper\SpecialListingMapper;
use App\Data\Database\Mapper\TimeIntMapper;
use Psr\Log\LoggerInterface;
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
    protected $pageCategoryMapper;
    protected $specialDayMapper;
    protected $specialBroadcastMapper;
    protected $normalBroadcastMapper;
    protected $timeIntMapper;
    protected $personMapper;
    protected $mailer;
    protected $captcha;

    protected $appConfigRequestFromAddress;
    protected $appConfigRequestToAddress;
    protected $appConfigSkipCaptcha;

    public function __construct(
        EntityManager $entityManager,
        PageMapper $pageMapper,
        PageCategoryMapper $pageCategoryMapper,
        PersonMapper $personMapper,
        ProgrammeMapper $programmeMapper,
        SpecialDayMapper $specialDayMapper,
        SpecialListingMapper $specialBroadcastMapper,
        NormalListingMapper $normalBroadcastMapper,
        TimeIntMapper $timeIntMapper,
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
        $this->programmeMapper = $programmeMapper;
        $this->imageMapper = $imageMapper;
        $this->pageMapper = $pageMapper;
        $this->specialDayMapper = $specialDayMapper;
        $this->specialBroadcastMapper = $specialBroadcastMapper;
        $this->normalBroadcastMapper = $normalBroadcastMapper;
        $this->timeIntMapper = $timeIntMapper;
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
}
