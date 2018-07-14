<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Service\ImagesService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ImagesConvertAction extends AbstractAdminController
{
    public function __invoke(
        Request $request,
        ImagesService $imagesService
    ): Response {

        $result = $imagesService->convertAll();
        return new Response($result);
    }
}
