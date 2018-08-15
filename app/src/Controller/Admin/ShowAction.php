<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Domain\Entity\Programme;
use App\Presenter\Message\ErrorMessage;
use App\Presenter\Message\OkMessage;
use App\Service\ImagesService;
use App\Service\PeopleService;
use App\Service\ProgrammesService;
use DateTimeImmutable;
use Ramsey\Uuid\UuidFactory;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ShowAction extends AbstractAdminController
{
    public function __invoke(
        Request $request,
        UuidFactory $uuidFactory,
        ProgrammesService $programmesService,
        PeopleService $peopleService,
        ImagesService $imagesService,
        DateTimeImmutable $now
    ): Response {

        $message = null;
        $showId = $uuidFactory->fromString($request->get('showId'));
        $show = $programmesService->findById($showId);
        if (!$show) {
            return $this->render404('No such show');
        }

        // if POST, parse the incoming JSON into appropriate calls
        if ($request->getMethod() === 'POST') {
            try {
                $this->handlePost($request, $show, $programmesService, $uuidFactory);
                $message = new OkMessage('Saved');
            } catch (\Exception $e) {
                $message = new ErrorMessage($e->getMessage());
            }

            // re-fetch the latest
            $show = $programmesService->findById($showId);
            if (!$show) {
                throw new RuntimeException('Something went very wrong here');
            }
        }

        $images = $imagesService->findAll();
        $people = $peopleService->findAll();
        $peopleInShow = $peopleService->findForProgramme($show);

        return $this->renderAdminSite(
            'show.html.twig',
            [
                'pageData' => \json_encode([
                    'message' => $message,
                    'show' => $show,
                    'images' => $images,
                    'people' => $people,
                    'selectedPeople' => $peopleInShow,
                    'types' => Programme::getAllTypesMapped(),
                ], JSON_PRETTY_PRINT),
            ],
            $request
        );
    }

    private function handlePost(
        Request $request,
        Programme $programme,
        ProgrammesService $programmesService,
        UuidFactory $uuidFactory
    ): void {
        // get all the field values
        $name = $request->get('name');
        $tagLine = $request->get('tagline');
        $type = (int)$request->get('type');
        $description = $request->get('html-content');

        $imageId = $request->get('image-id', null);
        if (!empty($imageId)) {
            $imageId = $uuidFactory->fromString($imageId);
        } else {
            $imageId = null;
        }

        $people = trim((string)$request->get('people'));

        $peopleIds = array_map(function ($id) use ($uuidFactory) {
            return $uuidFactory->fromString(trim($id));
        }, array_filter(explode(',', $people)));

        $programmesService->updateProgramme(
            $programme,
            $name,
            $tagLine,
            $type,
            $description,
            $imageId,
            $peopleIds
        );
    }
}
