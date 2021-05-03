<?php

namespace App\Service;

use App\Entity\TrickLibrary;

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
        if ($image->getFile())
        {
            $image->setTrick($this->trick);
            $fileName = 'snowtricks-'.uniqid().'.jpeg';
            $image->setLien($fileUploader->upload($image->getFile(), $fileName));
            $this->manager->persist($image);
            $this->manager->flush();
        }
    }

    /**
     * @param $image
     */
    function deleteImages($images, $newImages)
    {
        foreach ($images as $image) {
            $currentImageName = $image->getLien();

            $keepImage = false;
            // On recherche dans la liste des nouvelles images si les anciennes y sont.
            foreach ($newImages as $currentNewImage) {
                // Si c'est le cas, on sait qu'il ne faut pas les supprimer.
                if ($currentImageName == $currentNewImage->getLien()) {
                    $keepImage = true;
                    break;
                }
            }

            // Sinon, on peut supprimer l'ancienne image.
            if ($keepImage == false) {
                $this->manager->remove($image);
                if(file_exists('../public/img/tricks/' . $image->getLien())) {
                    unlink('../public/img/tricks/' . $image->getLien());
                }
            }
        }
        $this->manager->flush();
    }
}