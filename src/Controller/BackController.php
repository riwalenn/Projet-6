<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BackController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(UserRepository $userRepo, TrickRepository $trickRepo, CommentRepository $commentRepo)
    {
        $tricks = $trickRepo->findAll();
        $users = $userRepo->findAll();
        $comments = $commentRepo->findAll();

        return $this->render('back/index.html.twig', [
            'controller_name' => 'BackController',
            'tricks'          => $tricks,
            'users'           => $users,
            'comments'        => $comments]);
    }
}
