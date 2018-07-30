<?php
declare(strict_types=1);

namespace App\Controller\Schedules;

use App\Controller\AbstractController;
use App\Domain\Entity\SpecialDay;
use App\Presenter\CalendarMonthPresenter;
use App\Service\ConfigurableContentService;
use App\Service\PageService;
use App\Service\SchedulesService;
use DateTimeImmutable;
use DateTimeInterface;
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
        DateTimeInterface $date
    ): Response {

        $title = formatDateForDisplay($date);
        $specialDay = $this->schedulesService->getSpecialDay($date);
        if (!$specialDay) {
            return $this->renderDay(
                (int)$date->format('w'),
                $title
            );
        }

        return $this->renderMainSite(
            'schedules/show.html.twig',
            [
                'specialDay' => $specialDay,
                'title' => $title,
                'calendars' => $this->getCalendars(),
                'broadcasts' => $this->schedulesService->getShowsForSpecialDay($specialDay),
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
                'specialDay' => null,
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

        $specialDays = $this->schedulesService->getAllSpecialDaysAfter($startOfMonth);
        $specialFlags = $this->mapSpecialDaysToBooleanList($specialDays);
        $allDates = \array_keys($specialFlags);
        $end = new DateTimeImmutable(\end($allDates) . 'T23:59:59Z');

        $months = [];
        while ($startOfMonth < $end) {
            $months[] = new CalendarMonthPresenter($startOfMonth, $specialFlags);
            $startOfMonth = $startOfMonth->add(new \DateInterval('P1M'));
        }
        return $months;
    }

    private function mapSpecialDaysToBooleanList(array $specialDays): array
    {
        $days = [];
        foreach ($specialDays as $specialDay) {
            /** @var SpecialDay $specialDay */
            $days[$specialDay->getDate()->format('Y-m-d')] = true;
        }
        ksort($days);
        return $days;
    }

    private function getIntroduction()
    {
        return $this->pagesService->findByUrl(self::SPECIAL_PAGE_URL);
    }
}
