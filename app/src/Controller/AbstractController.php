<?php
declare(strict_types=1);

namespace App\Controller;

use App\Presenter\NavigationPresenter;
use App\Service\ConfigurableContentService;
use App\Service\PageService;
use App\Service\SchedulesService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyAbstractController;
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

    protected function renderMainSite(string $template, array $data = []): Response
    {
        $pages = $this->pageService->findAllForNavigation();

        $data['baseNavPresenter'] = new NavigationPresenter($pages);
        $data['baseFooterContent'] = $this->configurableContentService->getFooterContent();
        $data['baseNowAndNext'] = $this->schedulesService->getNowAndNext($this->now);
        $data['baseAssetManifest'] = json_decode(
            file_get_contents(__DIR__ . '/../../../public_html/static/assets-manifest.json'),
            true
        );
        return $this->render($template, $data);
    }
}
