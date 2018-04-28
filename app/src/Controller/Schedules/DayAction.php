<?php
declare(strict_types=1);

namespace App\Controller\Schedules;

use function App\Functions\DateTimes\weekdayFromDayNum;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DayAction extends AbstractSchedulesAction
{
    public function __invoke(
        Request $request
    ): Response {
        $day = $request->get('day');

        // convert day name to day number
        $dayNum = (int) date('w', strtotime($day));

        return $this->renderDay(
            $dayNum,
            weekdayFromDayNum($dayNum)
        );
    }
}
