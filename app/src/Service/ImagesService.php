<?php
declare(strict_types=1);

namespace App\Service;

use App\Data\Database\Entity\Image as DbImage;
use Doctrine\ORM\Query;
use Ramsey\Uuid\UuidInterface;

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
        $image = new DbImage(
            $title
        );
        $fileName = \strtolower($image->id->toString() . '.' . $fileExtension);
        $image->fileName = $fileName;

        $this->entityManager->persist($image);
        $this->entityManager->flush();

        return $fileName;
    }

    public function saveImage(string $fileName, $imageData): void
    {
        \file_put_contents(self::UPLOADED_FILE_PATH . $fileName, $imageData);
    }

    public function updateImageTitle(UuidInterface $imageId, string $newTitle): void
    {
        $image = $this->entityManager->getImageRepo()
            ->getByID($imageId, Query::HYDRATE_OBJECT);

        /** @var \App\Data\Database\Entity\Image $image */
        $image->title = $newTitle;
        $this->entityManager->persist($image);
        $this->entityManager->flush();
    }

    public function deleteImage(UuidInterface $imageId): void
    {
        /** @var \App\Data\Database\Entity\Image $image */
        $image = $this->entityManager->getImageRepo()
            ->getByID($imageId, Query::HYDRATE_OBJECT);

        $fileName = self::UPLOADED_FILE_PATH . $image->fileName;
        $this->entityManager->remove($image);
        $this->entityManager->flush();
        if (\file_exists($fileName)) {
            \unlink($fileName);
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
