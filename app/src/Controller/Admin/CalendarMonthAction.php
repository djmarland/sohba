<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Service\SchedulesService;
use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use function in_array;

class CalendarMonthAction extends AbstractAdminController
{
    public function __invoke(
        Request $request,
        SchedulesService $schedulesService
    ): Response {
        $year = (int)$request->get('year');
        $month = (int)$request->get('month');

        if (!checkdate($month, 1, $year)) {
            throw new NotFoundHttpException('No such date');
        }

        $date = new DateTimeImmutable();
        $date = $date->setTimezone(new DateTimeZone('Europe/London'));
        $date = $date->setDate($year, $month, 1);
        $date = $date->setTime(0, 0, 0);

        $endAt = $date->add(new DateInterval('P1M'));

        $dateFormat = 'Y-m-d';
        $specialDays = array_map(function (DateTimeImmutable $date) use ($dateFormat) {
            return $date->format($dateFormat);
        }, $schedulesService->getSpecialListingDates($date, $endAt));

        $days = [];
        $countDate = $date;
        while ($countDate < $endAt) {
            if (in_array($countDate->format($dateFormat), $specialDays, true)) {
                $isSpecial = true;
                $listings = $schedulesService->getShowsForSpecialDate($countDate);
            } else {
                $isSpecial = false;
                $listings = $schedulesService->getShowsForDay((int)$countDate->format('N'));
            }

            $days[] = [
                'date' => $countDate,
                'isSpecial' => $isSpecial,
                'listings' => $listings,
            ];
            $countDate = $countDate->add(new DateInterval('P1D'));
        }

        return $this->renderAdminSite(
            'calendar-month.html.twig',
            [
                'month' => $date,
                'days' => $days,
            ],
            $request
        );
    }
}
