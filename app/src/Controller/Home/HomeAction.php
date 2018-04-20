<?php
declare(strict_types=1);

namespace App\Controller\Home;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeAction extends AbstractController
{
    public function handleRequest(
        Request $request
    ): Response {
        return $this->render('home/home.html.twig', [
            'name' => 'rase',
        ]);
    }
}