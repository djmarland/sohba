<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Domain\Entity\Page;
use App\Domain\Entity\PageCategory;
use App\Service\PageService;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PagesAction extends AbstractAdminController
{
    public function __invoke(
        Request $request,
        PageService $pageService,
        DateTimeImmutable $now
    ): Response {
        // if POST, parse the incoming JSON into appropriate calls
        if ($request->getMethod() === 'POST') {
            if ($request->get('update-category')) {
                $categoryId = (int)$request->get('update-category');
                $newTitle = $request->get('category-title');
                $pageService->updatePageCategoryTitle($categoryId, $newTitle);
            } elseif ($request->get('delete-category')) {
                $categoryId = (int)$request->get('delete-category');
                $pageService->deletePageCategory($categoryId);
            } elseif ($request->get('delete-page')) {
                $pageId = (int)$request->get('delete-page');
                $pageService->deletePage($pageId);
            } elseif ($request->get('new-page-title')) {
                $title = $request->get('new-page-title');
                $pageId = $pageService->newPage($title);
                return $this->redirect('/admin/pages/' . $pageId);
            } elseif ($request->get('new-category-title')) {
                $title = $request->get('new-category-title');
                $pageService->newPageCategory($title);
            } elseif ($request->getContent()) {
                $data = \json_decode($request->getContent());
                foreach ($data as $catId => $position) {
                    $pageService->updateCategoryPosition((int)$catId, (int)$position);
                }
                return new Response('Ok');
            }
        }

        return $this->renderAdminSite(
            'pages.html.twig',
            [
                'pageData' => \json_encode($this->getData($pageService), JSON_PRETTY_PRINT),
            ],
            $request
        );
    }

    private function getData(PageService $pageService)
    {
        $categories = [];
        foreach ($pageService->findAllPageCategories() as $i => $category) {
            /** @var PageCategory $category */
            $categoryMap = [
                'id' => $category->getLegacyId(),
                'title' => $category->getTitle(),
                'position' => $i + 1,
                'pagesInCategory' => [],
            ];

            foreach ($pageService->findAllInCategory($category) as $j => $page) {
                /** @var Page $page */
                $categoryMap['pagesInCategory'][] = [
                    'id' => $page->getLegacyId(),
                    'title' => $page->getTitle(),
                    'position' => (($i + 1) * 100) + ($j + 1),
                ];
            }
            $categories[] = $categoryMap;
        }

        return [
            'categories' => $categories,
            'uncategorised' => array_map(function ($page) {
              /** @var Page $page */
                return [
                    'id' => $page->getLegacyId(),
                    'title' => $page->getTitle(),
                ];
            }, $pageService->findAllUncategorised()),
        ];
    }
}
