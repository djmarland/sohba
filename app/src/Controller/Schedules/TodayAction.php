<?php
declare(strict_types=1);

namespace App\Controller\Schedules;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TodayAction extends AbstractController
{
    public function handleRequest(
        Request $request
    ): Response {
        // todo - inject the current time and calculate today

        return $this->render('schedules/show.html.twig', [
            'date' => (new \DateTimeImmutable())->format('c'),
        ]);
    }
}