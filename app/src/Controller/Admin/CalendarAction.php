<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Service\SchedulesService;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CalendarAction extends AbstractAdminController
{
    public function __invoke(
        Request $request,
        SchedulesService $schedulesService,
        DateTimeImmutable $now
    ): Response {
        $schedulesService->migrate(); // todo - remove

        $specialDates = $schedulesService->getSpecialListingDates();
        $earliestDate = reset($specialDates);
        $latestDate = max(end($specialDates), $now);

        return $this->renderAdminSite(
            'calendar.html.twig',
            [
                'pageData' => \json_encode([
                    'earliestDate' => $earliestDate->format('c'),
                    'latestDate' => $latestDate->format('c'),
                    'highlightDates' => array_map(function(DateTimeImmutable $date) {
                        return $date->format('Y-m-d');
                    }, $specialDates),
                ], JSON_PRETTY_PRINT),
            ],
            $request
        );
    }
}
