<?php
declare(strict_types=1);

namespace App\Controller\Programmes;

use App\Controller\AbstractController;
use App\Service\ProgrammeService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ListAction extends AbstractController
{
    public function __invoke(
        Request $request,
        ProgrammeService $programmeService
    ): Response {

        $programmes = $programmeService->getAllActive();

        // todo - check it exists

        return $this->renderMainSite(
            'programmes/list.html.twig',
            [
                'programmes' => $programmes,
            ]
        );
    }
}
