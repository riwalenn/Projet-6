<?php


namespace App\Service;

use App\Entity\TrickHistory;

class TrickHelper
{
    private $trick;
    private $repository;
    private $manager;

    /**
     * TrickHelper constructor.
     * @param $tric
     * @param $repository
     * @param $manager
     */
    public function __construct($trick, $repository, $manager)
    {
        $this->trick = $trick;
        $this->repository = $repository;
        $this->manager = $manager;
    }

    /**
     * @param $user
     */
    function addModifiedBy($user)
    {
        $trickHistory = new TrickHistory();
        $author = $this->repository->findOneByCriteria("username", $this->trick->getUser());
        $trickHistory->setUser($user)
            ->setModifiedAt(new \DateTime())
            ->setTrick($this->trick);

        $this->manager->persist($trickHistory);
        $this->manager->flush();

        if ($user->getId() !== $author->getId()) {
            $serviceMail = new SendMail();
            $serviceMail->alertAuthor($author, $this->trick);
        }
    }
}