<?php
declare(strict_types=1);

namespace App\Controller\Images;

use App\Controller\AbstractController;
use App\Service\SchedulesService;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ShowAction extends AbstractController
{
    public function __invoke(
        Request $request
    ): Response {
        $width = (int)$request->get('width');
        $imageId = $request->get('id');

        $sourcePath = __DIR__ . '/../../../../uploaded_files/' .
            $imageId . '.jpg';

        if (!file_exists($sourcePath)) {
            return $this->render404('No such image');
        }

        [$originalWidth, $originalHeight] = getimagesize($sourcePath);

        $originalRatio = $originalWidth / $originalHeight;

        $height = $width / $originalRatio;

        // Resample
        $imageP = \imagecreatetruecolor($width, $height);
        $image = \imagecreatefromjpeg($sourcePath);
        \imagecopyresampled(
            $imageP,
            $image,
            0,
            0,
            0,
            0,
            $width,
            $height,
            $originalWidth,
            $originalHeight
        );

        return new BinaryFileResponse(\imagejpeg($imageP, null, 97));
    }
}
