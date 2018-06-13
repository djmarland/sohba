<?php
declare(strict_types=1);

namespace App\Controller\Page;

use App\Controller\AbstractController;
use App\Service\PageService;
use App\Service\SchedulesService;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SportsAction extends AbstractController
{
    public const SPECIAL_PAGE_URL = 'sports';

    public function __invoke(
        Request $request,
        SchedulesService $schedulesService,
        PageService $pageService,
        DateTimeImmutable $now
    ): Response {

        $upcomingSports = $schedulesService->findUpcomingSports($now);

        return $this->renderMainSite(
            'page/sports.html.twig',
            [
                'broadcasts' => $upcomingSports,
                'prose' => $pageService->findByUrl(self::SPECIAL_PAGE_URL),
            ]
        );
    }
}
