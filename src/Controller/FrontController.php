<?php

namespace App\Controller;

use App\Repository\CommentRepository;
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
        $tricks = $repository->findBy(array(), array('created_at' => 'DESC'), 3, 0);

        return $this->render('front/home.html.twig', [
            'controller_name' => 'FrontController',
            'title' => $this->title,
            'tricks' => $tricks]);
    }

    /**
     * @Route("/{offset}", name="more_tricks", requirements={"offset": "\d+"})
     */
    public function more_tricks(TrickRepository $repository, $offset = 6)
    {
        $tricks = $repository->findBy(array(), array('created_at' => 'DESC'), 3, $offset);

        return $this->render('front/tricks-more.html.twig', ['tricks' => $tricks]);
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
