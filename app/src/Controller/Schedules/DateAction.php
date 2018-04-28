<?php
declare(strict_types=1);

namespace App\Controller\Schedules;

use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DateAction extends AbstractSchedulesAction
{
    public function __invoke(
        Request $request
    ): Response {
        $year = (int) $request->get('year');
        $month = (int) $request->get('month');
        $day = (int) $request->get('day');

        if (!checkdate($month, $day, $year)) {
            throw new NotFoundHttpException('No such date');
        }

        $date = new DateTimeImmutable();
        $date = $date->setDate($year, $month, $day);

        return $this->renderDate($date);
    }
}
