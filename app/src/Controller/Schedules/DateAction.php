<?php
declare(strict_types=1);

namespace App\Controller\Schedules;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DateAction extends AbstractController
{
    public function handleRequest(
        Request $request
    ): Response {
        // todo - inject the current time and calculate today
        $year = $request->get('year');
        $month = $request->get('month');
        $day = $request->get('day');

        return $this->render('schedules/show.html.twig', [
            'date' => 'DATE ' . $year . '-' . $month . '-' . $day,
        ]);
    }
}