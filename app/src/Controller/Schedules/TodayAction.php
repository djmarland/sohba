<?php
declare(strict_types=1);

namespace App\Controller\Schedules;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TodayAction extends AbstractController
{
    public function __invoke(
        Request $request
    ): Response {
        // todo - inject the current time and calculate today

        $shows = [
            ['id' => 24 , 'time' => '00:00', 'title' => 'Around Midnight', 'synopsis' => 'Belinda Poore helps you relax through the early hours.',],
            ['id' => 25 , 'time' => '03:00', 'title' => 'Best Time Of The Day', 'synopsis' => 'Roy Stubbs keeps you amused and relaxed with music and his stories.',],
            ['id' => 26 , 'time' => '06:00', 'title' => 'Early Risers', 'synopsis' => 'Wake up with Nuala King every weekday morning.',],
            ['id' => 27 , 'time' => '08:00', 'title' => 'The Morning Show', 'synopsis' => 'Jim Adam plays some of the best in middle-of-the-road music.',],
            ['id' => 28 , 'time' => '11:00', 'title' => 'Coffee-time Selections', 'synopsis' => 'Carey Warden sorts out some easy listening music.',],
            ['id' => 29 , 'time' => '12:00', 'title' => 'Lunchtime Beat', 'synopsis' => 'Liz Allaway will make you want to tap your toes with her upbeat music.',],
            ['id' => 30 , 'time' => '14:00', 'title' => 'The Afternoon Show', 'synopsis' => 'Keeping you company with easy listening music while you relax after lunch.',],
        ];


        return $this->renderMainSite(
            'schedules/show.html.twig',
            [
                'date' => (new \DateTimeImmutable())->format('c'),
                'shows' => $shows,
            ]
        );
    }
}
