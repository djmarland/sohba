<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Domain\Entity\Page;
use App\Domain\Entity\PageCategory;
use App\Service\PageService;
use App\Service\ProgrammesService;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ShowsAction extends AbstractAdminController
{
    public function __invoke(
        Request $request,
        ProgrammesService $programmesService,
        DateTimeImmutable $now
    ): Response {

        $message = null;

        // if POST, parse the incoming JSON into appropriate calls
        if ($request->getMethod() === 'POST') {
            if ($request->get('new-show-name')) {
                $name = $request->get('new-show-name');
                $showId = $programmesService->newProgramme($name);
                return $this->redirect('/admin/shows/' . $showId);
            }

            if ($request->get('delete-show')) {
                $showId = (int)$request->get('delete-show');
                $programmesService->deleteProgramme($showId);
                $message = [
                    'type' => 'ok',
                    'message' => 'Show was deleted',
                ];
            }
        }

        return $this->renderAdminSite(
            'shows.html.twig',
            [
                'pageData' => \json_encode($this->getData($programmesService, $message), JSON_PRETTY_PRINT),
            ],
            $request
        );
    }

    private function getData(ProgrammesService $programmesService, ?array $message)
    {
        return [
            'regular' => $programmesService->getAllRegular(),
            'events' => $programmesService->getAllEvents(),
            'message' => $message,
        ];
    }
}
