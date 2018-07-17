<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Domain\Entity\Person;
use App\Service\ImagesService;
use App\Service\PeopleService;
use DateTimeImmutable;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PersonAction extends AbstractAdminController
{
    public function __invoke(
        Request $request,
        PeopleService $peopleService,
        ImagesService $imagesService,
        DateTimeImmutable $now
    ): Response {

        $message = null;
        $personId = $request->get('personId');
        $person = $peopleService->findByLegacyId((int)$personId);
        if (!$person) {
            return $this->render404('No such person');
        }

        // if POST, parse the incoming JSON into appropriate calls
        if ($request->getMethod() === 'POST') {
            try {
                $this->handlePost($request, $person, $peopleService);
                $message = [
                    'type' => 'ok',
                    'message' => 'Saved ',
                ];
            } catch (\Exception $e) {
                $message = [
                    'type' => 'error',
                    'message' => $e->getMessage(),
                ];
            }

            // re-fetch the latest
            $person = $peopleService->findByLegacyId((int)$personId);
            if (!$person) {
                throw new RuntimeException('Something went very wrong here');
            }
        }

        $images = $imagesService->findAll();

        return $this->renderAdminSite(
            'person.html.twig',
            [
                'pageData' => \json_encode([
                    'message' => $message,
                    'person' => $person,
                    'images' => $images,
                ], JSON_PRETTY_PRINT),
            ],
            $request
        );
    }

    private function handlePost(
        Request $request,
        Person $person,
        PeopleService $peopleService
    ): void {
        // get all the field values
        $name = $request->get('name');
        $onExec = (bool)$request->get('on-exec');

        $committeeTitle = null;
        $committeePosition = null;
        if ($onExec) {
            $committeeTitle = $request->get('exec-title');
            $committeePosition = (int)$request->get('exec-position');
        }

        $imageId = (int)$request->get('image-id') ?: null;

        $peopleService->updatePerson(
            $person,
            $name,
            $onExec,
            $committeeTitle,
            $committeePosition,
            $imageId
        );
    }
}
