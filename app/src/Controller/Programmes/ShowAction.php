<?php
declare(strict_types=1);

namespace App\Controller\Programmes;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ShowAction extends AbstractController
{
    public function __invoke(
        Request $request
    ): Response {
        $showId = $request->get('showId');

        // todo - check it exists

        return $this->renderMainSite(
            'programmes/show.html.twig',
            [
                'showId' => $showId,
            ]
        );
    }
}
