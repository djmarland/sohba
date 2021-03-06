<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Home\HomeAction;
use App\Controller\Page\OutsideBroadcastsAction;
use App\Controller\Page\PeopleAction;
use App\Controller\Page\RequestsAction;
use App\Controller\Page\SportsAction;
use App\Controller\Schedules\AbstractSchedulesAction;
use App\Domain\Entity\Page;
use App\Presenter\Message\ErrorMessage;
use App\Presenter\Message\OkMessage;
use App\Service\ImagesService;
use App\Service\PageService;
use DateTimeImmutable;
use Exception;
use InvalidArgumentException;
use Ramsey\Uuid\UuidFactory;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function json_encode;

class PageAction extends AbstractAdminController
{
    private const RESERVED_URLS = [
        HomeAction::SPECIAL_PAGE_URL => 'Home',
        AbstractSchedulesAction::SPECIAL_PAGE_URL => 'Programme Listings',
        RequestsAction::SPECIAL_PAGE_URL => 'Requests',
        SportsAction::SPECIAL_PAGE_URL => 'Sports',
        OutsideBroadcastsAction::SPECIAL_PAGE_URL => 'Outside Broadcasts',
        PeopleAction::SPECIAL_PAGE_URL => 'The People',
    ];

    public function __invoke(
        Request $request,
        UuidFactory $uuidFactory,
        PageService $pageService,
        ImagesService $imagesService,
        DateTimeImmutable $now
    ): Response {

        $message = null;
        $pageId = $uuidFactory->fromString($request->get('pageId'));
        $page = $pageService->findById($pageId);
        if (!$page) {
            return $this->render404('No such page');
        }

        // if POST, parse the incoming JSON into appropriate calls
        if ($request->getMethod() === 'POST') {
            try {
                $this->handlePost($request, $page, $pageService, $uuidFactory);
                $message = new OkMessage('Saved');
            } catch (Exception $e) {
                $message = new ErrorMessage($e->getMessage());
            }

            // re-fetch the latest
            $page = $pageService->findById($pageId);
            if (!$page) {
                throw new RuntimeException('Something went very wrong here');
            }
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
        $parts[] = 'admin';

        $urlRegex = '^(?!' . implode('|', $parts) . ')[a-z0-9-]+$';

        $images = $imagesService->findAll();

        return $this->renderAdminSite(
            'page.html.twig',
            [
                'pageData' => json_encode([
                    'message' => $message,
                    'page' => $page,
                    'images' => $images,
                    'allCategories' => $pageService->findAllPageCategories(),
                    'specialPages' => $specialPages,
                    'urlRegex' => $urlRegex,
                    'specialType' => array_key_exists(
                        (string)$page->getUrlPath(),
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
        PageService $pageService,
        UuidFactory $uuidFactory
    ): void {
        // get all the field values
        $title = $request->get('title');
        $specialPage = $request->get('special');
        $url = $request->get('url');
        $htmlContent = trim($request->get('html-content', ''));

        $navCategory = $request->get('nav-category');
        $navPosition = $request->get('nav-position');

        if (!empty($specialPage)) {
            if (!array_key_exists($specialPage, self::RESERVED_URLS)) {
                throw new InvalidArgumentException('Not a valid special page');
            }
            $url = $specialPage;
        }

        if (empty($url)) {
            throw new InvalidArgumentException('URL is required');
        }

        $pageService->updatePage(
            $page,
            $title,
            $url,
            $htmlContent,
            $navPosition ? (int)$navPosition : null,
            $navCategory ? $uuidFactory->fromString($navCategory) : null
        );
    }
}
