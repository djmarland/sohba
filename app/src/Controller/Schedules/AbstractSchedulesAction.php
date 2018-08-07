<?php
declare(strict_types=1);

namespace App\Controller\Schedules;

use App\Controller\AbstractController;
use App\Presenter\CalendarMonthPresenter;
use App\Service\ConfigurableContentService;
use App\Service\PageService;
use App\Service\SchedulesService;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Response;

use function App\Functions\DateTimes\formatDateForDisplay;

abstract class AbstractSchedulesAction extends AbstractController
{
    public const SPECIAL_PAGE_URL = 'schedules';

    private $schedulesService;
    private $pagesService;
    private $now;

    public function __construct(
        PageService $pageService,
        ConfigurableContentService $configurableContentService,
        SchedulesService $schedulesService,
        DateTimeImmutable $now
    ) {
        parent::__construct($pageService, $schedulesService, $configurableContentService, $now);
        $this->schedulesService = $schedulesService;
        $this->pagesService = $pageService;
        $this->now = $now;
    }

    protected function renderDate(
        DateTimeImmutable $date
    ): Response {
        $midnight = $date->setTime(0, 0, 0);

        $title = formatDateForDisplay($midnight);
        if (!$this->schedulesService->isSpecialDay($midnight)) {
            return $this->renderDay(
                (int)$midnight->format('N'),
                $title
            );
        }

        return $this->renderMainSite(
            'schedules/show.html.twig',
            [
                'title' => $title,
                'calendars' => $this->getCalendars(),
                'broadcasts' => $this->schedulesService->getShowsForSpecialDate($midnight),
                'prose' => $this->getIntroduction(),
            ]
        );
    }

    protected function renderDay(
        int $dayNum,
        string $title
    ): Response {
        return $this->renderMainSite(
            'schedules/show.html.twig',
            [
                'title' => $title,
                'calendars' => $this->getCalendars(),
                'broadcasts' => $this->schedulesService->getShowsForDay($dayNum),
                'prose' => $this->getIntroduction(),
            ]
        );
    }

    private function getCalendars()
    {
        // get all future special days, then create all months that cover them
        $startOfMonth = new DateTimeImmutable(
            $this->now->format('Y-m-') . '01T00:00:00Z'
        );

        $specialFlags = \array_map(function (DateTimeImmutable $date) {
            return $date->format('Y-m-d');
        }, $this->schedulesService->getSpecialListingDates($startOfMonth));
        $end = new DateTimeImmutable(\end($specialFlags) . 'T23:59:59Z');

        $months = [];
        while ($startOfMonth < $end) {
            $months[] = new CalendarMonthPresenter($startOfMonth, $specialFlags);
            $startOfMonth = $startOfMonth->add(new \DateInterval('P1M'));
        }
        return $months;
    }

    private function getIntroduction()
    {
        return $this->pagesService->findByUrl(self::SPECIAL_PAGE_URL);
    }
}
