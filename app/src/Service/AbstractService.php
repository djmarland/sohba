<?php
declare(strict_types=1);

namespace App\Service;

use App\Data\Database\EntityManager;
use App\Data\Database\Mapper\ImageMapper;
use App\Data\Database\Mapper\NormalListingMapper;
use App\Data\Database\Mapper\PageMapper;
use App\Data\Database\Mapper\ProgrammeMapper;
use App\Data\Database\Mapper\SpecialDayMapper;
use App\Data\Database\Mapper\SpecialListingMapper;
use App\Data\Database\Mapper\TimeIntMapper;
use Psr\Log\LoggerInterface;

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
    protected $specialDayMapper;
    protected $specialBroadcastMapper;
    protected $normalBroadcastMapper;
    protected $timeIntMapper;

    public function __construct(
        EntityManager $entityManager,
        PageMapper $pageMapper,
        ProgrammeMapper $programmeMapper,
        SpecialDayMapper $specialDayMapper,
        SpecialListingMapper $specialBroadcastMapper,
        NormalListingMapper $normalBroadcastMapper,
        TimeIntMapper $timeIntMapper,
        ImageMapper $imageMapper,
        LoggerInterface $logger
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
    }
}
