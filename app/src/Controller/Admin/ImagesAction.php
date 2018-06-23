<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Service\ImagesService;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ImagesAction extends AbstractAdminController
{
    public function __invoke(
        Request $request,
        ImagesService $imagesService,
        DateTimeImmutable $now
    ): Response {

        $okMessage = null;
        $errorMessage = null;

        if ($request->getMethod() === 'POST') {
            try {
                if ($request->files->get('chooseImage')) {
                    /** @var UploadedFile $file */
                    $file = $request->files->get('chooseImage');
                    $extension = $file->guessExtension();
                    if (!$extension) {
                        throw new \InvalidArgumentException('Could not recognise file type');
                    }
                    $title = $request->get('upImageTitle');
                    $fileName = $imagesService->newImage(
                        $title,
                        $extension
                    );

                    $file->move(
                        __DIR__ . '/../../../../uploaded_files',
                        $fileName
                    );

                    $okMessage = 'Image uploaded';
                }
            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();
            }
        }

        $images = $imagesService->findAll();

        return $this->renderAdminSite(
            'images.html.twig',
            [
                'okMessage' => $okMessage,
                'errorMessage' => $errorMessage,
                'images' => $images,
            ],
            $request
        );
    }
}
