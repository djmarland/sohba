<?php
declare(strict_types=1);

namespace App\Controller\Programmes;

use App\Controller\AbstractController;
use App\Domain\Entity\Person;
use App\Domain\Entity\Programme;
use App\Presenter\PersonPresenter;
use App\Service\PeopleService;
use App\Service\ProgrammesService;
use App\Service\SchedulesService;
use DateTimeImmutable;
use Ramsey\Uuid\UuidFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ShowAction extends AbstractController
{
    public function __invoke(
        Request $request,
        UuidFactory $uuidFactory,
        PeopleService $peopleService,
        ProgrammesService $programmesService,
        SchedulesService $schedulesService,
        DateTimeImmutable $now
    ): Response {

        $legacyShowId = $request->get('legacyShowId');
        if ($legacyShowId) {
            $programme = $programmesService->findByLegacyId((int)$legacyShowId);
            if (!$programme) {
                throw new NotFoundHttpException('No such programme');
            }
            return $this->redirectToRoute('programmes_show', [
                'showId' => (string)$programme->getId(),
            ], 301);
        }


        $showId = $request->get('showId');

        $programme = $programmesService->findById($uuidFactory->fromString($showId));
        if (!$programme) {
            throw new NotFoundHttpException('No such programme');
        }

        $nextOn = null;
        $listings = $this->getListings($schedulesService, $programme);
        if (!$listings) {
            $nextOn = $schedulesService->findNextForProgramme($programme, $now);
        }

        $people = $this->getPeoplePresenters($peopleService, $programmesService, $programme);

        return $this->renderMainSite(
            'programmes/show.html.twig',
            [
                'programme' => $programme,
                'listings' => $listings,
                'nextOn' => $nextOn,
                'people' => $people,
                'hasPeople' => !empty($people),
            ]
        );
    }

    private function getListings(
        SchedulesService $schedulesService,
        Programme $programme
    ): ?array {
        $results = $schedulesService->getListingsForProgramme($programme);
        return (!empty($results)) ? $results : null;
    }

    private function getPeoplePresenters(
        PeopleService $peopleService,
        ProgrammesService $programmesService,
        Programme $currentProgramme
    ) {
        $people = $peopleService->findForProgramme($currentProgramme);
        if (empty($people)) {
            return [];
        }

        $peopleProgrammes = $programmesService->getAllByPeople($currentProgramme);

        $peoplePresenters = array_map(function (Person $person) use ($peopleProgrammes) {
            return new PersonPresenter($person, $peopleProgrammes);
        }, $people);

        return $peoplePresenters;
    }
}
