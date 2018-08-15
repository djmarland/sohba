<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Presenter\Message\AbstractMessagePresenter;
use App\Presenter\Message\OkMessage;
use App\Service\ProgrammesService;
use DateTimeImmutable;
use Ramsey\Uuid\UuidFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ShowsAction extends AbstractAdminController
{
    public function __invoke(
        Request $request,
        UuidFactory $uuidFactory,
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
                $showId = $uuidFactory->fromString($request->get('delete-show'));
                $programmesService->deleteProgramme($showId);
                $message = new OkMessage('Show was deleted');
            } elseif ($request->getContent()) {
                $data = \json_decode($request->getContent(), true);

                $programmesService->newProgramme(
                    $data['showName'],
                    $data['showType']
                );

                $programmes = $data['includeAll'] ?
                    $programmesService->getAll() : $programmesService->getAllRegular();
                return new JsonResponse(
                    $programmes
                );
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

    private function getData(ProgrammesService $programmesService, ?AbstractMessagePresenter $message)
    {
        return [
            'regular' => $programmesService->getAllRegular(),
            'events' => $programmesService->getAllEvents(),
            'message' => $message,
        ];
    }
}
