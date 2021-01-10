<?php
declare(strict_types=1);

namespace App\Controller\Images;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function file_exists;

class ShowAction extends AbstractController
{
    public function __invoke(
        Request $request
    ): Response {
        $imageId = $request->get('id');
        $ext = $request->get('ext');

        $sourcePath = __DIR__ . '/../../../../uploaded_files/' .
            $imageId . '.' . $ext;

        if (!file_exists($sourcePath)) {
            return $this->render404('No such image');
        }

        return new BinaryFileResponse($sourcePath, 200, [
            'cache-control' => 'public, max-age=' . (60*60*24*365),
        ]);
    }
}
