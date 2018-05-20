<?php
declare(strict_types=1);

namespace App\Controller;

use App\Presenter\NavigationPresenter;
use App\Service\ConfigurableContentService;
use App\Service\PageService;
use App\Service\SchedulesService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyAbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractController extends SymfonyAbstractController
{
    private $pageService;
    private $configurableContentService;
    private $schedulesService;
    private $now;

    public function __construct(
        PageService $pageService,
        SchedulesService $schedulesService,
        ConfigurableContentService $configurableContentService,
        DateTimeImmutable $now
    ) {
        $this->pageService = $pageService;
        $this->configurableContentService = $configurableContentService;
        $this->schedulesService = $schedulesService;
        $this->now = $now;
    }

    protected function renderMainSite(
        string $template,
        array $data = [],
        ?Request $request = null,
        ?Response $originalResponse = null
    ): Response {
        $pages = $this->pageService->findAllForNavigation();

        $data['baseNavPresenter'] = new NavigationPresenter($pages);
        $data['baseFooterContent'] = $this->configurableContentService->getFooterContent();
        $data['baseNowAndNext'] = $this->schedulesService->getNowAndNext($this->now);
        $data['baseAssetManifest'] = json_decode(
            file_get_contents(__DIR__ . '/../../../public_html/static/assets-manifest.json'),
            true
        );
        $data['baseShowCricket'] = false;
        if ($request && $request->get('crickettest')) {
            $data['baseShowCricket'] = true;
        } elseif (
            isset($data['baseNowAndNext'][0]->programme) &&
            $data['baseNowAndNext'][0]->programme->isCricket()
        ) {
            $data['baseShowCricket'] = true;
        }

        return $this->render($template, $data, $originalResponse);
    }

    protected function render404(string $message)
    {
        return $this->renderMainSite(
            'error/404.html.twig',
            [
                'message' => $message,
            ],
            null,
            new Response('', Response::HTTP_NOT_FOUND)
        );
    }
}
