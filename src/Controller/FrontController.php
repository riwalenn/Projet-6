<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class FrontController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param TrickRepository $repository
     * @param CacheInterface $cache
     * @return Response
     * @throws InvalidArgumentException
     */
    public function home(TrickRepository $repository, CacheInterface $cache)
    {
        $tricks = $cache->get('tricks', function (ItemInterface $item) use ($repository) {
            $item->expiresAfter(360);
           return $repository->findTricksAndFirstMedia(0,3);
        });

        return $this->render('front/home.html.twig', [
            'title'             => "Bienvenue sur le site communautaire de SnowTricks !",
            'totalTricks'       => count($repository->findAll()),
            'tricks'            => $tricks
        ]);
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
        return $this->render('front/tricks-more.html.twig', [
            'tricks' => $repository->findTricksAndFirstMedia($limit,3)
        ]);
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
