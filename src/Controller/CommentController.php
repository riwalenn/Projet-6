<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{

    /**
     * @Route("/trick/comment", name="new_comment")
     * @param Trick $trick
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return FormView
     */
    public function newComment(Trick $trick, Request $request, EntityManagerInterface $manager): FormView
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $trick->addComment($comment);
            $comment->setCreatedAt(new \DateTime());
            $comment->setUser($this->getUser());
            $manager->persist($comment);
            $manager->flush();
        }
        return $form->createView();
    }
}
