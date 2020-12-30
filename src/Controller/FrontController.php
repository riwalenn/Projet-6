<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param TrickRepository $repository
     * @return Response
     */
    public function home(TrickRepository $repository)
    {
        $tricks = $repository->findBy(array(), array('created_at' => 'DESC'), 3, 0);

        return $this->render('front/home.html.twig', [
            'controller_name'   => 'FrontController',
            'title'             => "Bienvenue sur le site communautaire de SnowTricks !",
            'tricks'            => $tricks]);
    }

    /**
     * More medias on home
     *
     * @Route("/{offset}", name="more_tricks", requirements={"offset": "\d+"})
     * @param TrickRepository $repository
     * @param int $offset
     * @return Response
     */
    public function more_tricks(TrickRepository $repository, $offset = 3)
    {
        $tricks = $repository->findBy(array(), array('created_at' => 'DESC'), 3, $offset);

        return $this->render('front/tricks-more.html.twig', ['tricks' => $tricks]);
    }

    /**
     * User profil
     *
     * @Route("/profil/{id}", name="profil")
     * @param TrickRepository $repo
     * @param CommentRepository $commentRepository
     * @return Response
     */
    public function show_profil(TrickRepository $repo, CommentRepository $commentRepository)
    {
        $user = $this->getUser();
        $tricks = $repo->findBy(array("User" => $user), array('id' => 'DESC'));
        $comments = $commentRepository->findBy(array("User" => $user), array('id' => 'DESC'));

        return $this->render('front/profil.html.twig', [
            'title'         => "Bienvenue ".$user->getUsername()." sur le site de SnowTricks !",
            'tricks'        => $tricks,
            'comments'      => $comments
        ]);
    }
}
