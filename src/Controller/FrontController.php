<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\TrickHistory;
use App\Repository\TrickHistoryRepository;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    private $title = "Bienvenue sur le site communautaire de SnowTricks !";

    /**
     * @Route("/front", name="front")
     */
    public function index(TrickRepository $repository)
    {
        $tricks = $repository->findAll();
        return $this->render('front/index.html.twig', [
            'controller_name' => 'FrontController',
            'title' => $this->title,
            'tricks' => $tricks
        ]);
    }

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
     * @Route("/tricks_detail/{id}", name="trick_detail")
     */
    public function tricks_detail(Trick $trick, TrickHistoryRepository $historyRepository)
    {
        $trick_history = $historyRepository->findAll();
        return $this->render('front/tricks-details.html.twig', [
            'title' => "Tricks",
            'trick' => $trick,
            'trick_history' => $trick_history
        ]);
    }
}
