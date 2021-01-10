<?php
declare(strict_types=1);

namespace App\Functions\DateTimes;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use InvalidArgumentException;

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
 * The standard formatting for this application to display a short date
 * @param DateTimeInterface $date
 * @return string
 */
function formatShortDateForDisplay(DateTimeInterface $date): string
{
    return $date->format('j M');
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

function dayNameToDate(string $dayName): DateTimeImmutable
{
    $date = DateTimeImmutable::createFromFormat('l', $dayName, new DateTimeZone('Z'));
    if (!$date) {
        throw new InvalidArgumentException('Date could not be created');
    }
    return $date;
}

function dayNumToDate(int $dayNum): DateTimeImmutable
{
    $date = new DateTimeImmutable('now', new DateTimeZone('Z'));
    return $date->setISODate(1, 1, $dayNum);
}

function isoWeekdayToPHPWeekDay(int $dayNum): int
{
    if ($dayNum === 7) {
        return 0;
    }
    return $dayNum;
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
