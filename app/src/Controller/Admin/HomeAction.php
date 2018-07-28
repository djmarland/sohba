<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeAction extends AbstractAdminController
{
    public function __invoke(
        Request $request,
        DateTimeImmutable $now
    ): Response {
        return $this->renderAdminSite(
            'home.html.twig',
            [],
            $request
        );
    }
}
