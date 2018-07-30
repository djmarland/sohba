<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use function App\Functions\DateTimes\dayNameToDate;
use function App\Functions\DateTimes\dayNumToDate;
use App\Presenter\Message\ErrorMessage;
use App\Presenter\Message\OkMessage;
use App\Service\ProgrammesService;
use App\Service\SchedulesService;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NormalListingsDayAction extends AbstractAdminController
{
    public function __invoke(
        Request $request,
        DateTimeImmutable $now,
        ProgrammesService $programmesService,
        SchedulesService $schedulesService,
        string $day
    ): Response {
        $currentDay = dayNameToDate($day);

        $dayNav = array_map(function ($dayNum) use ($currentDay) {
            $date = dayNumToDate($dayNum);
            $dayName = $date->format('l');
            return [
                'title' => $dayName,
                'link' => '/admin/normal-listings/' . strtolower($dayName),
                'active' => $date->format('N') === $currentDay->format('N'),
            ];
        }, range(1, 7));

        $message = null;

        if ($request->getMethod() === 'POST' && $request->get('listings')) {
            try {
                $data = \json_decode($request->get('listings'), true);
                $schedulesService->updateNormalListings((int)$currentDay->format('N'), array_map(function ($inputObj) {
                    return [
                        'time' => DateTimeImmutable::createFromFormat('H:i', $inputObj['time']),
                        'programme' => $inputObj['programmeLegacyId'],
                    ];
                }, $data));
                $message = new OkMessage('Saved');
            } catch (\Exception $e) {
                $message = new ErrorMessage($e->getMessage());
            }
        }

        $dayListings = $schedulesService->getShowsForDay((int)$currentDay->format('N'));
        $regularProgrammes = $programmesService->getAllRegular();

        return $this->renderAdminSite(
            'normal-listings.html.twig',
            [
                'dayNav' => $dayNav,
                'dayTitle' => ucfirst($day),
                'pageData' => \json_encode([
                    'message' => $message,
                    'dayListings' => $dayListings,
                    'programmes' => $regularProgrammes,
                ], JSON_PRETTY_PRINT),
            ],
            $request
        );
    }
}
