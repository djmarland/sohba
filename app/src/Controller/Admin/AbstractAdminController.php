<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Service\ConfigurableContentService;
use App\Service\PageService;
use App\Service\SchedulesService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyAbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractAdminController extends SymfonyAbstractController
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

    protected function renderAdminSite(
        string $template,
        array $data = [],
        ?Request $request = null,
        ?Response $originalResponse = null
    ): Response {
        return $this->render($template, $data, $originalResponse);
    }
}
