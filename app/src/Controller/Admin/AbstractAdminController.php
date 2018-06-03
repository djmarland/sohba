<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractAdminController extends AbstractController
{
    protected function renderAdminSite(
        string $template,
        array $data = [],
        ?Request $request = null,
        ?Response $originalResponse = null
    ): Response {
        $data['baseAssetManifest'] = $this->getAssetManifest();
        return $this->render('admin/' . $template, $data, $originalResponse);
    }
}
