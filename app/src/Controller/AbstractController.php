<?php
declare(strict_types=1);

namespace App\Controller;

use App\Presenter\NavigationPresenter;
use App\Service\PageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyAbstractController;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractController extends SymfonyAbstractController
{
    private $pageService;

    public function __construct(
        PageService $pageService
    ) {
        $this->pageService = $pageService;
    }

    protected function renderMainSite(string $template, array $data = []): Response
    {
        $pages = $this->pageService->findAllForNavigation();

        $data['baseNavPresenter'] = new NavigationPresenter($pages);
        $data['baseAssetManifest'] = json_decode(
            file_get_contents(__DIR__ . '/../../../public_html/static/assets-manifest.json'),
            true
        );
        return $this->render($template, $data);
    }
}
