<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Presenter\Message\ErrorMessage;
use App\Presenter\Message\OkMessage;
use App\Service\ProgrammesService;
use App\Service\SchedulesService;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CalendarAction extends AbstractAdminController
{
    public function __invoke(
        Request $request,
        SchedulesService $schedulesService,
        ProgrammesService $programmesService,
        // todo - remove
        DateTimeImmutable $now
    ): Response {
        $schedulesService->migrate(); // todo - remove
        $programmesService->migrate(); // todo - remove

        $message = null;
        // if POST, parse the incoming JSON into appropriate calls
        if ($request->getMethod() === 'POST' && $request->get('delete-month')) {
            try {
                $date = new DateTimeImmutable($request->get('delete-month') . '-01');
                if (!$date) {
                    throw new \InvalidArgumentException('Not a valid date in the format YYYY-MM');
                }

                $endDate = $date->add(new \DateInterval('P1M'));

                $schedulesService->deleteSpecialBetween($date, $endDate);
                $message = new OkMessage('Listings for ' . $date->format('F Y') . ' were successfully deleted');
            } catch (\Exception $e) {
                $message = new ErrorMessage('An error occurred: ' . $e);
            }
        }

        $specialDates = $schedulesService->getSpecialListingDates();
        $earliestDate = reset($specialDates);
        $latestDate = max(end($specialDates), $now);

        return $this->renderAdminSite(
            'calendar.html.twig',
            [
                'pageData' => \json_encode([
                    'message' => $message,
                    'earliestDate' => $earliestDate->format('c'),
                    'latestDate' => $latestDate->format('c'),
                    'highlightDates' => array_map(function (DateTimeImmutable $date) {
                        return $date->format('Y-m-d');
                    }, $specialDates),
                ], JSON_PRETTY_PRINT),
            ],
            $request
        );
    }
}
