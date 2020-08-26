<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    private $title = "Bienvenue sur le site communautaire de SnowTricks !";

    /**
     * @Route("/front", name="front")
     */
    public function index()
    {
        return $this->render('front/index.html.twig', [
            'controller_name' => 'FrontController',
            'title' => $this->title
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('front/home.html.twig', ['title' => $this->title]);
    }

    /**
     * @Route("/tricks", name="tricks")
     */
    public function tricks()
    {
        return $this->render('front/tricks-details.html.twig', ['title' => "Tricks"]);
    }
}
