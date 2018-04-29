<?php
declare(strict_types=1);

namespace App\Controller\Programmes;

use App\Controller\AbstractController;
use App\Domain\Entity\Programme;
use App\Service\ProgrammesService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ListAction extends AbstractController
{
    public function __invoke(
        Request $request,
        ProgrammesService $programmeService
    ): Response {

        $programmes = $programmeService->getAllActive();

        $letterGroups = [];
        foreach ($programmes as $programme) {
            /** @var Programme $programme $l */
            $l = $programme->getLetterGroup();
            if (!isset($letterGroups[$l])) {
                $letterGroups[$l] = [];
            }
            $letterGroups[$l][] = $programme;
        }

        return $this->renderMainSite(
            'programmes/list.html.twig',
            [
                'letterGroups' => $letterGroups,
            ]
        );
    }
}
