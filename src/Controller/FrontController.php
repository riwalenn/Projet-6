<?php

namespace App\Controller;

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
}
