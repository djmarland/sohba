<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Service\ConfigurableContentService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class KeyValueAction extends AbstractAdminController
{
    public function __invoke(
        Request $request,
        ConfigurableContentService $configurableContentService
    ): Response {
        $configurableContentService->ensureKeysExist();

        $all = $configurableContentService->getAll();

        return $this->renderAdminSite(
            'key-value.html.twig',
            [
                'allKeys' => $all,
            ],
            $request
        );
    }
}
