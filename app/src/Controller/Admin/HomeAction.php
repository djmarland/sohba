<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeAction extends AbstractAdminController
{
    public function __invoke(
        Request $request,
        DateTimeImmutable $now
    ): Response {
        $earliestDate = new DateTimeImmutable('2014-04-02');

        $latestDate = $now; // todo - latest is latest special listing or today

        return $this->renderAdminSite(
            'home.html.twig',
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
