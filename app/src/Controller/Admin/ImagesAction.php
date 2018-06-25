<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Presenter\Message\ErrorMessage;
use App\Presenter\Message\OkMessage;
use App\Service\ImagesService;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ImagesAction extends AbstractAdminController
{
    public function __invoke(
        Request $request,
        ImagesService $imagesService,
        DateTimeImmutable $now
    ): Response {

        $message = null;

        if ($request->getMethod() === 'POST') {
            try {
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

                file_put_contents(__DIR__ . '/../../../../uploaded_files/' . $fileName, $imageData);
                return new JsonResponse([
                    'message' => new OkMessage('Image uploaded successfully'),
                    'images' => $imagesService->findAll(),
                ]);
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
}
