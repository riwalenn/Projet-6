<?php


namespace App\Service;


class VideoHelper
{
    private $trick;
    private $manager;

    public function __construct($trick, $manager)
    {
        $this->trick = $trick;
        $this->manager = $manager;
    }

    /**
     * @param $video
     */
    function addVideos($video)
    {
        if ($video->getLien())
        {
            $video->setTrick($this->trick);
            $this->manager->persist($video);
            $this->manager->flush();
        }
    }

    /**
     * @param $videos
     */
    function deleteVideos($videos)
    {
        foreach ($videos as $video)
        {
            $this->manager->remove($video);
            $this->manager->flush();
        }
    }
}