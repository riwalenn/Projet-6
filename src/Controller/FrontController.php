<?php

namespace App\Controller;

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
        $tricks = $repository->findTricksAndFirstMedia(0,3);
        $totalTricks = count($repository->findAll());

        return $this->render('front/home.html.twig', [
            'controller_name'   => 'FrontController',
            'title'             => "Bienvenue sur le site communautaire de SnowTricks !",
            'totalTricks'       => $totalTricks,
            'tricks'            => $tricks]);
    }

    /**
     * More medias on home
     *
     * @Route("/{limit}", name="load_more", requirements={"limit": "\d+"})
     * @param TrickRepository $repository
     * @param int $offset
     * @return Response
     */
    public function loadMore(TrickRepository $repository, int $limit = 3)
    {
        $tricks = $repository->findTricksAndFirstMedia($limit,3);

        return $this->render('front/tricks-more.html.twig', ['tricks' => $tricks]);
    }

    /**
     * User profil
     *
     * @Route("/profil/{id}", name="profil")
     * @return Response
     */
    public function profil()
    {
        return $this->render('front/profil.html.twig', [
            'title'         => "Bienvenue sur le site de SnowTricks !"
        ]);
    }
}
