<?php
declare(strict_types=1);

namespace App\Service;

use App\Data\Database\Entity\Image as DbImage;
use App\Data\ID;
use Doctrine\ORM\Query;

class ImagesService extends AbstractService
{
    private const UPLOADED_FILE_PATH = __DIR__ . '/../../../uploaded_files/';

    public function findAll(): array
    {
        return $this->mapMany(
            $this->entityManager->getImageRepo()->findAll(),
            $this->imageMapper
        );
    }

    public function newImage(
        string $title,
        string $fileExtension
    ): string {

        $uuid = ID::makeNewID(DbImage::class);
        $fileName = \strtolower($uuid->toString() . '.' . $fileExtension);

        $image = new DbImage(
            $uuid,
            $title
        );
        $image->fileName = $fileName;

        $this->entityManager->persist($image);
        $this->entityManager->flush();

        return $fileName;
    }

    public function convertAll()
    {
        // fetch all images that don't have a fileName
        $uncoverted = $this->entityManager->getImageRepo()
            ->findAllUnconverted(Query::HYDRATE_OBJECT);

        $convertedCount = 0;

        foreach ($uncoverted as $originalImage) {
            try {
                /** @var DbImage $originalImage */

                $ext = $this->imageTypeToExtension($originalImage->type);

                $uuid = ID::makeNewID(DbImage::class);
                $fileName = \strtolower($uuid->toString() . '.' . $ext);

                $this->saveImage($fileName, $originalImage->data);

                $originalImage->id = $uuid;
                $originalImage->uuid = (string)$uuid;
                $originalImage->fileName = $fileName;

                $this->entityManager->persist($originalImage);
                $convertedCount++;
            } catch (\Throwable $exception) {
                // do nothing, move along
            }
        }

        $this->entityManager->flush();
        return $convertedCount . '/' . \count($uncoverted);
    }

    public function saveImage(string $fileName, $imageData): void
    {
        \file_put_contents(self::UPLOADED_FILE_PATH . $fileName, $imageData);
    }

    public function updateImageTitle(int $imageId, string $newTitle): void
    {
        $image = $this->entityManager->getImageRepo()
            ->findByLegacyId($imageId, Query::HYDRATE_OBJECT);

        /** @var \App\Data\Database\Entity\Image $image */
        $image->title = $newTitle;
        $this->entityManager->persist($image);
        $this->entityManager->flush();
    }

    public function deleteImage(int $imageId): void
    {
        $image = $this->entityManager->getImageRepo()
            ->findByLegacyId($imageId, Query::HYDRATE_OBJECT);

        $this->entityManager->getImageRepo()->deleteByLegacyId($imageId);

        /** @var \App\Data\Database\Entity\Image $image */
        if ($image->fileName) {
            \unlink(self::UPLOADED_FILE_PATH . $image->fileName);
        }
    }

    private function imageTypeToExtension(string $type): string
    {
        switch ($type) {
            case 'image/png':
                return 'png';
            case 'image/jpeg':
            default:
                return 'jpg';
        }
    }
}
