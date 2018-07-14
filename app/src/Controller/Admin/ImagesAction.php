<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Presenter\Message\ErrorMessage;
use App\Presenter\Message\OkMessage;
use App\Service\ImagesService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ImagesAction extends AbstractAdminController
{
    public function __invoke(
        Request $request,
        ImagesService $imagesService
    ): Response {

        $message = null;

        if ($request->getMethod() === 'POST') {
            try {
                if ($request->get('update-image')) {
                    $imageId = (int)$request->get('update-image');
                    $newTitle = $request->get('image-title');
                    $imagesService->updateImageTitle($imageId, $newTitle);
                    $message = new OkMessage('Image title was updated');
                } elseif ($request->get('delete-image')) {
                    $imageId = (int)$request->get('delete-image');
                    $imagesService->deleteImage($imageId);
                    $message = new OkMessage('Image was deleted');
                } else {
                    // any other upload type is an image data upload
                    return $this->handleUpload($request, $imagesService);
                }
            } catch (\Exception $e) {
                $message = new ErrorMessage($e->getMessage());
            }
        }

        $images = $imagesService->findAll();

        return $this->renderAdminSite(
            'images.html.twig',
            [
                'pageData' => \json_encode([
                    'message' => $message,
                    'images' => $images,
                ], JSON_PRETTY_PRINT),
            ],
            $request
        );
    }

    private function handleUpload(
        Request $request,
        ImagesService $imagesService
    ): JsonResponse {
        $content = $request->getContent();

        $parts = \explode(',', $content);
        $imageData = \base64_decode(\end($parts));

        $mimeParts = \explode(';', \reset($parts));
        switch (\reset($mimeParts)) {
            case 'data:image/jpeg':
                $extension = 'jpg';
                break;
            case 'data:image/png':
                $extension = 'png';
                break;
            default:
                throw new \InvalidArgumentException('Unrecognised image type');
        }

        $fileName = $imagesService->newImage(
            '',
            $extension
        );

        $imagesService->saveImage($fileName, $imageData);

        return new JsonResponse([
            'message' => new OkMessage('Image uploaded successfully'),
            'images' => $imagesService->findAll(),
        ]);
    }
}
