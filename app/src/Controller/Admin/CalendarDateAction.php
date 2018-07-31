<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CalendarDateAction extends AbstractAdminController
{
    public function __invoke(
        Request $request,
        DateTimeImmutable $now
    ): Response {
        // sooooooon - todo
        return $this->renderAdminSite(
            'calendar.html.twig',
            [
                'pageData' => \json_encode([
                    'earliestDate' => $earliestDate->format('c'),
                    'latestDate' => $latestDate->format('c'),
                ], JSON_PRETTY_PRINT),
            ],
            $request
        );
    }
}
