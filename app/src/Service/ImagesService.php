<?php
declare(strict_types=1);

namespace App\Service;

use App\Data\Database\Entity\Image as DbImage;
use App\Data\ID;

class ImagesService extends AbstractService
{
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
        $fileName = strtolower($uuid->toString() . '.' . $fileExtension);

        $image = new DbImage(
            $uuid,
            $title
        );
        $image->fileName = $fileName;

        $this->entityManager->persist($image);
        $this->entityManager->flush();

        return $fileName;
    }
}
