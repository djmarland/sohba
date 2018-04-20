<?php
declare(strict_types=1);

namespace App\Controller\Page;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ShowAction extends AbstractController
{
    public function handleRequest(
        Request $request
    ): Response {
        $page = $request->get('page');

        // todo - check it exists

        return $this->render('page/show.html.twig', [
            'page' => $page,
        ]);
    }
}