<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Domain\Entity\Programme;
use App\Service\ImagesService;
use App\Service\ProgrammesService;
use DateTimeImmutable;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ShowAction extends AbstractAdminController
{
    public function __invoke(
        Request $request,
        ProgrammesService $programmesService,
        ImagesService $imagesService,
        DateTimeImmutable $now
    ): Response {

        $message = null;
        $showId = $request->get('showId');
        $show = $programmesService->findByLegacyId((int)$showId);
        if (!$show) {
            return $this->render404('No such show');
        }

        // if POST, parse the incoming JSON into appropriate calls
        if ($request->getMethod() === 'POST') {
            try {
                $this->handlePost($request, $show, $programmesService);
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
            $show = $programmesService->findByLegacyId((int)$showId);
            if (!$show) {
                throw new RuntimeException('Something went very wrong here');
            }
        }

        $images = $imagesService->findAll();

        return $this->renderAdminSite(
            'show.html.twig',
            [
                'pageData' => \json_encode([
                    'message' => $message,
                    'show' => $show,
                    'images' => $images,
                    'types' => Programme::getAllTypesMapped(),
                ], JSON_PRETTY_PRINT),
            ],
            $request
        );
    }

    private function handlePost(
        Request $request,
        Programme $programme,
        ProgrammesService $programmesService
    ): void {
        // get all the field values
        $name = $request->get('name');

        $imageId = (int)$request->get('image-id') ?: null;

        $programmesService->updateProgramme(
            $programme,
            $name,
            $imageId
        );
    }
}
