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

        $data['baseSiteTitle'] = $this->configurableContentService->getValue(
            ConfigurableContentService::KEY_WEBSITE_TITLE
        );
        ;
        $data['baseNavPresenter'] = new NavigationPresenter($pages);
        $data['baseFooterContent'] = $this->configurableContentService->getValue(
            ConfigurableContentService::KEY_FOOTER_CONTENT
        );
        $data['baseNowAndNext'] = $this->schedulesService->getNowAndNext($this->now);
        $data['baseAssetManifest'] = $this->getAssetManifest();
        $data['baseShowCricket'] = null;
        if ($request && $request->get('crickettest')) {
            $data['baseShowCricket'] = $this->configurableContentService->getValue(
                ConfigurableContentService::KEY_CRICKET_STREAM_URL
            );
        } elseif (isset($data['baseNowAndNext'][0]) &&
            $data['baseNowAndNext'][0]->getProgramme()->isCricket()
        ) {
            $data['baseShowCricket'] = $this->configurableContentService->getValue(
                ConfigurableContentService::KEY_CRICKET_STREAM_URL
            );
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

    protected function getAssetManifest()
    {
        return json_decode(
            file_get_contents(__DIR__ . '/../../public/static/assets-manifest.json'),
            true
        );
    }
}
