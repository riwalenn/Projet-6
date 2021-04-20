<?php


namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
     * @param UploadedFile $file
     * @return string
     */
    function uploader($file)
    {
        $fileName = 'snowtricks-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->getParameter('imgTricks_directory'), $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $fileName;
    }

    /**
     * @param $file
     * @param $library
     * @param $trick
     * @param $manager
     * @throws \Exception
     */
    function addImages($image)
    {
        if ($image->getLien())
        {
            $image->setLien($this->uploader($image));
            $image->setTrick($this->trick);
            $this->manager->persist($image);
            $this->manager->flush();
        }
    }

    /**
     * @param $images
     * @param $manager
     */
    function deleteImages($images)
    {
        foreach ($images as $image)
        {
            $this->manager->remove($image);
            $this->manager->flush();
        }
    }
}