<?php
declare(strict_types=1);

namespace App\Controller\Home;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeAction extends AbstractController
{
    public function __invoke(
        Request $request
    ): Response {
        return $this->renderMainSite(
            'home/home.html.twig',
            [
                'name' => 'rase',
            ]
        );
    }
}
