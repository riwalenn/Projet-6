<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
use App\Form\ChangeRoleType;
use App\Repository\CommentRepository;
use App\Repository\TrickLibraryRepository;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
    public function show(UserRepository $userRepo, TrickRepository $trickRepo, TrickLibraryRepository $libraryRepository, CommentRepository $commentRepo, Request $request, EntityManagerInterface $manager)
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
     * @Route("/admin/user/{id}/change/role", name="change_role")
     * @param User $user
     * @param EntityManagerInterface $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function changeRole(User $user, EntityManagerInterface $manager)
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
     * @Route("/admin/{id}/delete/comment", name="admin_delete_comment")
     * @param Comment $comment
     * @param EntityManagerInterface $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteComment(Comment $comment, EntityManagerInterface $manager)
    {
        $manager->remove($comment);
        $manager->flush();
        $this->addFlash('success', 'Le commentaire a bien été supprimé !');

        return $this->redirectToRoute('admin');
    }

    /**
     * @Route("/admin/{id}/delete/user", name="admin_delete_user")
     * @param User $user
     * @param EntityManagerInterface $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteUser(User $user, EntityManagerInterface $manager)
    {
        $manager->remove($user);
        $manager->flush();

        $this->addFlash('success', 'L\'utilisateur a bien été supprimé !');
        return $this->redirectToRoute('admin');
    }

    /**
     * @Route("/admin/{id}/delete/trick", name="admin_delete_trick")
     * @param Trick $trick
     * @param EntityManagerInterface $manager
     * @return RedirectResponse
     */
    public function deleteTrick(Trick $trick, EntityManagerInterface $manager)
    {
        $manager->remove($trick);
        $manager->flush();
        $this->addFlash('success', 'Le trick a bien été supprimé !');
        return $this->redirectToRoute('admin');
    }
}
