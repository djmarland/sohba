<?php
declare(strict_types=1);

namespace App\Functions\DateTimes;

use DateTimeInterface;

/**
 * The standard formatting for this application to display a whole date
 * @param DateTimeInterface $date
 * @return string
 */
function formatDateForDisplay(DateTimeInterface $date): string
{
    return $date->format('l jS F Y');
}

/**
 * The standard formatting for this application to display a month
 * @param DateTimeInterface $date
 * @return string
 */
function formatMonthForDisplay(DateTimeInterface $date): string
{
    return $date->format('F Y');
}

function weekdayFromDayNum(int $dayNum): string
{
    return [
        'Sunday',
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
    ][$dayNum];
}
