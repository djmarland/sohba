<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Domain\Entity\Page;
use App\Service\PageService;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PageAction extends AbstractAdminController
{
    private const RESERVED_URLS = [
        'home' => 'Home',
        'requests' => 'Requests',
        'sports' => 'Sports',
        'outside-broadcasts' => 'Outside Broadcasts',
        'people' => 'The People',
    ];

    public function __invoke(
        Request $request,
        PageService $pageService,
        DateTimeImmutable $now
    ): Response {

        $messageOk = null;
        $messageFail = null;
        $pageId = $request->get('pageId');
        $page = $pageService->findByLegacyId((int)$pageId);
        if (!$page) {
            return $this->render404('No such page');
        }

        // if POST, parse the incoming JSON into appropriate calls
        if ($request->getMethod() === 'POST') {
            try {
                $this->handlePost($request, $page, $pageService);
                $messageOk = 'Saved';
            } catch (\Exception $e) {
                $messageFail = $e->getMessage();
            }

            // re-fetch the latest
            $page = $pageService->findByLegacyId((int)$pageId);

        }

        $specialPages = [];
        foreach (self::RESERVED_URLS as $value => $title) {
            $specialPages[] = [
                'value' => $value,
                'title' => $title,
            ];
        }

        $parts = array_map(function ($part) {
            return $part . '$';
        }, array_keys(self::RESERVED_URLS));

        $urlRegex = '^(?!' . implode('|', $parts) . ')[a-z0-9-]+$';

        return $this->renderAdminSite(
            'page.html.twig',
            [
                'pageData' => \json_encode([
                    'messageOk' => $messageOk,
                    'messageFail' => $messageFail,
                    'page' => $page,
                    'allCategories' => $pageService->findAllPageCategories(),
                    'specialPages' => $specialPages,
                    'urlRegex' => $urlRegex,
                    'specialType' => array_key_exists(
                        $page->getUrlPath(),
                        self::RESERVED_URLS
                    ) ? $page->getUrlPath() : '',
                ], JSON_PRETTY_PRINT),
            ],
            $request
        );
    }

    private function handlePost(
        Request $request,
        Page $page,
        PageService $pageService
    ) {
        // get all the field values
        $title = $request->get('title');
        $specialPage = $request->get('special');
        $url = $request->get('url');
        $content = $request->get('content');
        $navCategory = $request->get('nav-category');
        $navPosition = $request->get('nav-position');

        if (!empty($specialPage)) {
            if (!array_key_exists($specialPage, self::RESERVED_URLS)) {
                throw new \InvalidArgumentException('Not a valid special page');
            }
            $url = $specialPage;
        }

        if (empty($url)) {
            throw new \InvalidArgumentException('URL is required');
        }

        $pageService->updatePage(
            $page,
            $title,
            $url,
            $content,
            $navPosition ? (int) $navPosition : null,
            $navCategory ? (int) $navCategory : null
        );
    }
}