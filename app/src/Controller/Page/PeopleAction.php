<?php
declare(strict_types=1);

namespace App\Controller\Page;

use App\Controller\AbstractController;
use App\Domain\Entity\Person;
use App\Presenter\PersonPresenter;
use App\Service\PageService;
use App\Service\PeopleService;
use App\Service\ProgrammesService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PeopleAction extends AbstractController
{
    public const SPECIAL_PAGE_URL = 'people';

    public function __invoke(
        PeopleService $peopleService,
        ProgrammesService $programmesService,
        PageService $pageService,
        Request $request
    ): Response {

        $executiveCommittee = $peopleService->findExecutiveCommittee();
        $members = $peopleService->findOtherMembers();

        $shows = $programmesService->getAllByPersonIds();

        $executiveCommittee = array_map(function (Person $person) use ($shows) {
           return new PersonPresenter($person, $shows);
        }, $executiveCommittee);

        $members = array_map(function (Person $person) use ($shows) {
            return new PersonPresenter($person, $shows);
        }, $members);

        return $this->renderMainSite(
            'page/people.html.twig',
            [
                'committee' => $executiveCommittee,
                'members' => $members,
                'prose' => $pageService->findByUrl(self::SPECIAL_PAGE_URL),
            ]
        );
    }
}
