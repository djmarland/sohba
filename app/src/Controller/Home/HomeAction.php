<?php
declare(strict_types=1);

namespace App\Controller\Home;

use App\Controller\AbstractController;
use App\Service\PageService;
use App\Service\SchedulesService;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeAction extends AbstractController
{
    public const SPECIAL_PAGE_URL = 'home';

    public function __invoke(
        Request $request,
        SchedulesService $schedulesService,
        PageService $pageService,
        DateTimeImmutable $now
    ): Response {
        return $this->renderMainSite(
            'home/home.html.twig',
            [
                'sports' => $schedulesService->findUpcomingSports($now, 3),
                'events' => $schedulesService->findUpcomingOutsideBroadcasts($now, 3),
                'prose' => $pageService->findByUrl(self::SPECIAL_PAGE_URL),
            ],
            $request
        );
    }
}
