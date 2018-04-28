<?php
declare(strict_types=1);

namespace App\Controller\Schedules;

use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Response;

class TodayAction extends AbstractSchedulesAction
{
    public function __invoke(
        DateTimeImmutable $now
    ): Response {
        return $this->renderDate($now);
    }
}
