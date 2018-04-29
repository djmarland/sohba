<?php
declare(strict_types=1);

namespace App\Controller\Page;

use App\Controller\AbstractController;
use App\Service\SchedulesService;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OutsideBroadcastsAction extends AbstractController
{
    public function __invoke(
        Request $request,
        SchedulesService $schedulesService,
        DateTimeImmutable $now
    ): Response {
        $upcomingSports = $schedulesService->findUpcomingOutsideBroadcasts($now);

        return $this->renderMainSite(
            'page/outside-broadcasts.html.twig',
            [
                'broadcasts' => $upcomingSports,
            ]
        );
    }
}
