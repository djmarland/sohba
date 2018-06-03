<?php
declare(strict_types=1);

namespace App\Controller\Home;

use App\Controller\AbstractController;
use App\Service\SchedulesService;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeAction extends AbstractController
{
    public function __invoke(
        Request $request,
        SchedulesService $schedulesService,
        DateTimeImmutable $now
    ): Response {
        return $this->renderMainSite(
            'home/home.html.twig',
            [
                'sports' => $schedulesService->findUpcomingSports($now, 3),
                'events' => $schedulesService->findUpcomingOutsideBroadcasts($now, 3),
            ],
            $request
        );
    }
}
