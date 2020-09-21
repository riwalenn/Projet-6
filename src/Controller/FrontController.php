<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
use App\Repository\CommentRepository;
use App\Repository\TrickHistoryRepository;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    private $title = "Bienvenue sur le site communautaire de SnowTricks !";


    /**
     * @Route("/", name="home")
     */
    public function home(TrickRepository $repository)
    {
        $tricks = $repository->findAll();

        return $this->render('front/home.html.twig', [
            'controller_name' => 'FrontController',
            'title' => $this->title,
            'tricks' => $tricks]);
    }

    /**
     * @Route("/profil/{id}", name="profil")
     */
    public function show_profil(TrickRepository $repo, CommentRepository $commentRepository)
    {
        $user = $this->getUser();
        $tricks = $repo->findBy(array("User" => $user), array('id' => 'DESC'));
        $comments = $commentRepository->findBy(array("User" => $user), array('id' => 'DESC'));
        return $this->render('front/profil.html.twig', [
            'title' => $this->title,
            'tricks' => $tricks,
            'comments' => $comments
        ]);
    }
}
