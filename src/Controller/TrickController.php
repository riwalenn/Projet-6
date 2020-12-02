<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Comment;
use App\Entity\TrickHistory;
use App\Entity\TrickLibrary;
use App\Form\TrickType;
use App\Repository\TrickLibraryRepository;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use App\Service\SendMail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TrickHistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use FilesystemIterator;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;

class TrickController extends AbstractController
{
    private $title = "Bienvenue sur le site communautaire de SnowTricks !";

    /**
     * @Route("/trick_detail/{id}", name="trick_detail")
     */
    public function trick_detail(Trick $trick, Request $request, EntityManagerInterface $manager, TrickHistoryRepository $historyRepository, TrickLibraryRepository $libraryRepository)
    {
        $trick_history = $historyRepository->findAll();
        $itemsLibrary = $libraryRepository->findBy(array('trick' => $trick->getId()), array(), 3, 0);
        $comment = new Comment();
        $form = $this->createFormBuilder($comment)
            ->add('title')
            ->add('content')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTime())
                ->setTrick($trick)
                ->setUser($this->getUser());

            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('trick_detail', ['id' => $trick->getId()]);
        }

        return $this->render('front/tricks-details.html.twig', [
            'title'             => "Tricks",
            'trick'             => $trick,
            'itemsLibrary'      => $itemsLibrary,
            'trick_history'     => $trick_history,
            'commentForm'       => $form->createView()
        ]);
    }

    /**
     * @Route("/trick_detail/{id}/{offset}", name="more_medias", requirements={"offset": "\d+"})
     */
    public function more_medias(Trick $trick, TrickLibraryRepository $libraryRepository, $offset = 6)
    {
        $medias = $libraryRepository->findBy(array('trick' => $trick->getId()), array(), 3, $offset);

        return $this->render('front/medias-more.html.twig', ['medias' => $medias,'trick' => $trick,]);
    }

    /**
     * @Route("/front/new_trick", name="add_trick")
     * @Route("/front/{id}/edit", name="edit_trick")
     *
     * @return string
     */
    public function form_trick(Trick $trick = null, TrickRepository $repository, UserRepository $repo, Request $request, EntityManagerInterface $manager)
    {
        if (!$trick) {
            $trick = new Trick();
        }

        $user = $this->getUser();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);


        $position = $trick->getPosition();
        $grabs = $trick->getGrabs();
        $rotation = $trick->getRotation();
        $flip = $trick->getFlip();
        $slide = $trick->getSlide();
        $title = $position. ' ' . $grabs . ' à ' . $rotation . '° ' . $flip . ' ' . $slide;

        $result = $repository->findOneBy(['title' => $title]);


        if ($form->isSubmitted() && $form->isValid()) {
            if (!$trick->getId()) {
                $trick->setTitle($title);
                $trick->setCreatedAt(new \DateTime());
                $trick->setUser($user);
                $files = $form->get('image')->getData();
                if ($files) {
                    $listFiles = new FilesystemIterator('img/tricks', FilesystemIterator::SKIP_DOTS);
                    $count = iterator_count($listFiles);
                    $newFileId = $count + 1;

                    $newFileName = 'snowtricks-' . $newFileId . '.' . $files->guessExtension();

                    try {
                        $files->move($this->getParameter('imgTricks_directory'), $newFileName);
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    $trick->setImage($newFileName);
                }
                if (!empty($result)) {
                    $this->addFlash('red', "Le trick existe déjà !");
                    return $this->redirectToRoute('add_trick');
                }
            } else {
                $trick->setTitle($title);
                $author = $repo->findOneByCriteria("username", $trick->getUser());
                if ($user->getId() !== $author->getId()) {
                    $trickHistory = new TrickHistory();
                    $trickHistory->setUser($user)
                                ->setTrick($trick)
                                ->setModifiedAt(new \DateTime());
                    $manager->persist($trickHistory);
                    $serviceMail = new SendMail();
                    $serviceMail->alertAuthor($author, $trick);
                }
            }
            $manager->persist($trick);
            $manager->flush();

            $this->addFlash('light', "Le trick a été modifié avec succès !");

            return $this->redirectToRoute('home');
        }

        if ($trick) {
            $title = $trick->getTitle();
        } else {
            $title = "Ajouter un trick.";
        }

        return $this->render('front/tricks-form.html.twig', [
            'title'         => $title,
            'formTrick'     => $form->createView(),
            'editMode'      => $trick->getId() !== null
        ]);
    }

    public function deleteAction(Trick $trick, EntityManagerInterface $manager)
    {
        foreach ($trick->getTrickLibraries() as $library) {
            $trick->removeTrickLibrary($library);
            $manager->remove($library);
        }
        foreach ($trick->getComments() as $comment) {
            $trick->removeComment($comment);
            $manager->remove($comment);
        }
        foreach ($trick->getTrickHistories() as $trickHistory) {
            $trick->removeTrickHistory($trickHistory);
            $manager->remove($trickHistory);
        }

        $manager->remove($trick);
        $manager->flush();
        $this->addFlash('success', 'Le trick a bien été supprimé !');
    }

    /**
     * @Route("/trick_detail/{id}/add_media/", name="add_media")
     */
    public function add_trick_media()
    {

    }

    /**
     * @Route("/delete_trick/{id}", name="delete_trick")
     */
    public function delete_trick(Trick $trick, EntityManagerInterface $manager)
    {
        $this->deleteAction($trick, $manager);
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/admin/{id}/delete_trick", name="admin_delete_trick")
     */
    public function delete_admin_trick(Trick $trick, EntityManagerInterface $manager)
    {
        $this->deleteAction($trick, $manager);
        return $this->redirectToRoute('admin');
    }
}
