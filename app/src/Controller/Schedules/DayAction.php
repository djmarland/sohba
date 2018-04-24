<?php
declare(strict_types=1);

namespace App\Controller\Schedules;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DayAction extends AbstractController
{
    public function __invoke(
        Request $request
    ): Response {
        // todo - inject the current time and calculate today
        $day = $request->get('day');

        return $this->renderMainSite(
            'schedules/show.html.twig',
            [
                'date' => 'DAY ' . $day,
            ]
        );
    }
}
