<?php
declare(strict_types=1);

namespace App\Controller\Programmes;

use App\Controller\AbstractController;
use App\Domain\Entity\Broadcast;
use App\Domain\Entity\Programme;
use App\Service\ProgrammesService;
use App\Service\SchedulesService;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ShowAction extends AbstractController
{
    public function __invoke(
        Request $request,
        ProgrammesService $programmesService,
        SchedulesService $schedulesService,
        DateTimeImmutable $now
    ): Response {
        $showId = $request->get('showId');

        $programme = $programmesService->findByLegacyId((int)$showId);
        if (!$programme) {
            throw new NotFoundHttpException('No such programme');
        }

        $nextOn = null;
        $listings = $this->getListings($schedulesService, $programme);
        if (!$listings) {
            $nextOn = $schedulesService->findNextForProgramme($programme, $now);
        }

        return $this->renderMainSite(
            'programmes/show.html.twig',
            [
                'programme' => $programme,
                'listings' => $listings,
                'nextOn' => $nextOn,
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
}
