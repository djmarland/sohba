<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Service\SchedulesService;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeAction extends AbstractAdminController
{
    public function __invoke(
        Request $request,
        SchedulesService $schedulesService,
        DateTimeImmutable $now
    ): Response {
        return $this->renderAdminSite(
            'admin/home.html.twig',
            [],
            $request
        );
    }
}
