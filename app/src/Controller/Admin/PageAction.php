<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Domain\Entity\Page;
use App\Domain\Entity\PageCategory;
use App\Service\PageService;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PageAction extends AbstractAdminController
{
    public function __invoke(
        Request $request,
        PageService $pageService,
        DateTimeImmutable $now
    ): Response {

        $pageId = $request->get('pageId');
        $page = $pageService->findByLegacyId((int) $pageId);
        if (!$page) {
            return $this->render404('No such page');
        }

        // if POST, parse the incoming JSON into appropriate calls
        if ($request->getMethod() === 'POST') {

            // re-fetch the latest
            $page = $pageService->findByLegacyId((int) $pageId);

        }


        return $this->renderAdminSite(
            'page.html.twig',
            [
                'pageData' => \json_encode(['page' => $page], JSON_PRETTY_PRINT),
            ],
            $request
        );
    }
}