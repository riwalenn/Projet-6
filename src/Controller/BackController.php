<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\User;
use App\Form\ChangeRoleType;
use App\Repository\CommentRepository;
use App\Repository\TrickLibraryRepository;
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
     * @param UserRepository $userRepo
     * @param TrickRepository $trickRepo
     * @param TrickLibraryRepository $libraryRepository
     * @param CommentRepository $commentRepo
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(UserRepository $userRepo, TrickRepository $trickRepo, TrickLibraryRepository $libraryRepository, CommentRepository $commentRepo, Request $request, EntityManagerInterface $manager)
    {
        $tricks = $trickRepo->findAll();
        $users = $userRepo->findAll();
        $comments = $commentRepo->findAll();
        $itemslibrary = $libraryRepository->findAll();
        $user = new User();
        $form = $this->createForm(ChangeRoleType::class, $user);
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
            'itemslibrary'    => $itemslibrary,
            'comments'        => $comments,
            'lastMigration'   => $this->getLastMigrationVersion()]);
    }

    protected function getLastMigrationVersion()
    {
        $files = scandir('../migrations', SCANDIR_SORT_DESCENDING);
        $lastMigration = $files[0];
        $lastMigration = str_replace("Version", "", $lastMigration);
        $lastMigration = str_replace(".php", "", $lastMigration);

        return $lastMigration;
    }

    /**
     * @Route("/admin/user/{id}/change_role", name="change_role")
     * @param User $user
     * @param EntityManagerInterface $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function change_Admin_Role(User $user, EntityManagerInterface $manager)
    {
        if($user->getRoles()[0] === "ROLE_ADMIN") {
            $user->setRoles((array)'ROLE_USER');
        } elseif($user->getRoles()[0] === "ROLE_USER") {
            $user->setRoles((array)'ROLE_ADMIN');
        }

        $manager->persist($user);
        $manager->flush();

        return $this->redirectToRoute('admin');
    }

    /**
     * @Route("/admin/{id}/delete_comment", name="admin_delete_comment")
     * @param Comment $comment
     * @param EntityManagerInterface $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
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
     * @param User $user
     * @param EntityManagerInterface $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
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
