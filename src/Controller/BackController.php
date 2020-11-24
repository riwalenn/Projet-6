<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\User;
use App\Form\ChangeRoleType;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class BackController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(UserRepository $userRepo, TrickRepository $trickRepo, CommentRepository $commentRepo, Request $request, EntityManagerInterface $manager)
    {
        $tricks = $trickRepo->findAll();
        $users = $userRepo->findAll();
        $comments = $commentRepo->findAll();
        $user = new User();
        $form = $this->createForm(ChangeRoleType::class, $user);
        /*$form = $this->createFormBuilder($user)
            ->add('roles')
            ->getForm();*/
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);
            $manager->flush();
        }

        return $this->render('back/index.html.twig', [
            'controller_name' => 'BackController',
            'userForm'        => $form->createView(),
            'tricks'          => $tricks,
            'users'           => $users,
            'comments'        => $comments]);
    }

    /**
     * @Route("/admin/{id}/delete_comment", name="admin_delete_comment")
     */
    public function delete_Admin_Comment(Comment $comment, EntityManagerInterface $manager)
    {
        $manager->remove($comment);
        $manager->flush();
        $this->addFlash('success', 'Le commentaire a bien été supprimé !');

        return $this->redirectToRoute('admin');
    }

    /**
     * @Route("/admin/{id}/delete_user", name="admin_delete_user")
     */
    public function delete_Admin_User(User $user, EntityManagerInterface $manager)
    {
        foreach ($user->getComments() as $comment)
        {
            $user->removeComment($comment);
            $manager->remove($comment);
        }
        foreach ($user->getTrickHistories() as $trickHistory)
        {
            $user->removeTrickHistory($trickHistory);
            $manager->remove($trickHistory);
        }
        $user->setUsername('anonyme');
        $user->setEmail('anonyme@none.com');
        $manager->flush();
        return $this->redirectToRoute('admin');
    }
}
