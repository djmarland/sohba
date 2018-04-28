<?php
declare(strict_types=1);

namespace App\Controller\Page;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RequestsAction extends AbstractController
{
    public function __invoke(
        Request $request
    ): Response {

        return $this->renderMainSite(
            'page/requests.html.twig'
        );
    }
}
