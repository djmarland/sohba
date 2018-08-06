<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Service\ConfigurableContentService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TechInfoAction extends AbstractAdminController
{
    public function __invoke(
        Request $request,
        ConfigurableContentService $configurableContentService
    ): Response {
        return $this->renderAdminSite(
            'tech-info.html.twig',
            [
                'htmlContent' => $configurableContentService->getValue(
                    ConfigurableContentService::KEY_X_TECHNICAL_DETAILS
                ),
            ],
            $request
        );
    }
}
