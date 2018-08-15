<?php
declare(strict_types=1);

namespace App\Controller\Page;

use App\Controller\AbstractController;
use App\Service\PageService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ShowAction extends AbstractController
{
    public function __invoke(
        Request $request,
        PageService $pageService
    ): Response {
        $pageId = $request->get('page');

        $page = $pageService->findByUrl($pageId);

        if (!$page) {
            return $this->render404('No such page');
        }

        return $this->renderMainSite(
            'page/show.html.twig',
            [
                'page' => $page,
            ]
        );
    }
}
