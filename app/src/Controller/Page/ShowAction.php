<?php
declare(strict_types=1);

namespace App\Controller\Page;

use App\Controller\AbstractController;
use App\Service\PageService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ShowAction extends AbstractController
{
    public function __invoke(
        Request $request,
        PageService $pageService
    ): Response {
        $pageId = $request->get('page');

        if (is_numeric($pageId)) {
            $page = $pageService->findByLegacyId((int) $pageId);
        } else {
            // todo - elseif for a string based entry
            throw new NotFoundHttpException('No such page'); // todo - graceful 404
        }

        return $this->renderMainSite(
            'page/show.html.twig',
            [
                'page' => $page,
            ]
        );
    }
}
