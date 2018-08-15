<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Domain\Entity\Programme;
use App\Presenter\Message\AbstractMessagePresenter;
use App\Presenter\Message\ErrorMessage;
use App\Presenter\Message\OkMessage;
use App\Service\ProgrammesService;
use App\Service\SchedulesService;
use DateTimeImmutable;
use Ramsey\Uuid\UuidFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CalendarDateAction extends AbstractAdminController
{
    public function __invoke(
        Request $request,
        ProgrammesService $programmesService,
        SchedulesService $schedulesService,
        UuidFactory $uuidFactory
    ): Response {
        $year = (int)$request->get('year');
        $month = (int)$request->get('month');
        $day = (int)$request->get('day');

        if (!checkdate($month, $day, $year)) {
            throw new NotFoundHttpException('No such date');
        }

        $date = new DateTimeImmutable();
        $date = $date->setTime(0, 0, 0);
        $date = $date->setDate($year, $month, $day);

        $message = null;
        if ($request->getMethod() === 'POST') {
            $message = $this->handlePost($request, $date, $schedulesService, $uuidFactory);
        }

        // get the listings
        $isSpecial = $schedulesService->isSpecialDay($date);
        if ($isSpecial) {
            $listings = $schedulesService->getShowsForSpecialDate($date);
        } else {
            $listings = $schedulesService->getShowsForDay((int)$date->format('N'));
        }

        // get all the programmes
        $programmes = $programmesService->getAll();

        return $this->renderAdminSite(
            'calendar-date.html.twig',
            [
                'date' => $date,
                'isSpecial' => $isSpecial,
                'message' => $message,
                'pageData' => \json_encode([
                    'message' => $message,
                    'listings' => $listings,
                    'allProgrammes' => $programmes,
                    'types' => Programme::getAllTypesMapped(),
                ], JSON_PRETTY_PRINT),
            ],
            $request
        );
    }

    private function handlePost(
        Request $request,
        DateTimeImmutable $date,
        SchedulesService $schedulesService,
        UuidFactory $uuidFactory
    ): AbstractMessagePresenter {
        try {
            if ($request->get('delete-day')) {
                $tomorrow = $date->add(new \DateInterval('P1D'));
                $schedulesService->deleteSpecialBetween($date, $tomorrow);
                return new OkMessage(
                    $date->format('l jS F Y') . ' has been reset to normal listings'
                );
            }
            if ($request->get('listings')) {
                $data = \json_decode($request->get('listings'), true);
                $schedulesService->updateSpecialListings(
                    $date,
                    array_map(function ($inputObj) use ($date, $uuidFactory) {
                        $time = DateTimeImmutable::createFromFormat('H:i', $inputObj['time']);
                        $time = $time->setDate(
                            (int)$date->format('Y'),
                            (int)$date->format('m'),
                            (int)$date->format('d')
                        );
                        return [
                            'time' => $time,
                            'programme' => $uuidFactory->fromString($inputObj['programmeId']),
                            'internalNote' => !empty($inputObj['internalNote']) ? $inputObj['internalNote'] : null,
                            'publicNote' => !empty($inputObj['publicNote']) ? $inputObj['publicNote'] : null,
                        ];
                    }, $data)
                );
                return new OkMessage('Saved');
            }
            return new ErrorMessage('I do not know what you did');
        } catch (\Exception $e) {
            return new ErrorMessage('An error occurred: ' . $e);
        }
    }
}
