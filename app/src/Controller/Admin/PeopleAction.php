<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Service\PeopleService;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PeopleAction extends AbstractAdminController
{
    public function __invoke(
        Request $request,
        PeopleService $peopleService,
        DateTimeImmutable $now
    ): Response {

        $message = null;

        // if POST, parse the incoming JSON into appropriate calls
        if ($request->getMethod() === 'POST') {
            if ($request->get('new-person-name')) {
                $name = $request->get('new-person-name');
                $personId = $peopleService->newPerson($name);
                return $this->redirect('/admin/people/' . $personId);
            }

            if ($request->get('delete-person')) {
                $personId = (int)$request->get('delete-person');
                $peopleService->deletePerson($personId);
                $message = [
                    'type' => 'ok',
                    'message' => 'Person was deleted',
                ];
            }
        }

        return $this->renderAdminSite(
            'people.html.twig',
            [
                'pageData' => \json_encode([
                    'message' => $message,
                    'people' => $peopleService->findAll(),
                ], JSON_PRETTY_PRINT),
            ],
            $request
        );
    }
}
