<?php

namespace App\Service;

use App\Entity\TrickLibrary;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImagesHelper
{
    private $trick;
    private $manager;

    public function __construct($trick, $manager)
    {
        $this->trick = $trick;
        $this->manager = $manager;
    }

    /**
     * @param $image
     */
    function addImages(TrickLibrary $image, $fileUploader)
    {
        if ($image->getLien())
        {
            $image->setTrick($this->trick);
            $fileName = 'snowtricks-'.uniqid().'.jpeg';
            $uploadedFile = new UploadedFile($image->getLien(), $fileName);
            $image->setLien($fileUploader->upload($uploadedFile));
            $this->manager->persist($image);
            $this->manager->flush();
        }
    }

    /**
     * @param $image
     */
    function deleteImages($images)
    {
        foreach ($images as $image) {
            $this->manager->remove($image);
            if(file_exists('../public/img/tricks/' . $image->getLien())) {
                unlink('../public/img/tricks/' . $image->getLien());
            }

            $this->manager->flush();
        }
    }
}