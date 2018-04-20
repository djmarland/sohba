<?php
declare(strict_types=1);

namespace App\Controller\Programmes;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ListAction extends AbstractController
{
    public function handleRequest(
        Request $request
    ): Response {
        // todo - get alphabetical list of shows that have been allocated a future timeslot

        return $this->render('programmes/list.html.twig');
    }
}