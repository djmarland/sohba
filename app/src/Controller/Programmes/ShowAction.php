<?php
declare(strict_types=1);

namespace App\Controller\Programmes;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ShowAction extends AbstractController
{
    public function handleRequest(
        Request $request
    ): Response {
        $showId = $request->get('showId');

        // todo - check it exists

        return $this->render('programmes/show.html.twig', [
            'showId' => $showId,
        ]);
    }
}