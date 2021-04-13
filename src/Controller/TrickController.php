<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\TrickHistory;
use App\Entity\TrickLibrary;
use App\Form\TrickType;
use App\Framework\Constantes;
use App\Repository\TrickLibraryRepository;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use App\Service\SendMail;
use App\Service\Slugify;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use FilesystemIterator;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;

class TrickController extends AbstractController
{
    /**
     * @Route("/trick/{slug}", name="trick_detail")
     * @param Trick $trick
     * @param CommentController $commentController
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param EntityManagerInterface $manager
     * @param TrickLibraryRepository $libraryRepository
     * @return Response
     */
    public function show(Trick $trick, CommentController $commentController, Request $request, PaginatorInterface $paginator, EntityManagerInterface $manager, TrickLibraryRepository $libraryRepository)
    {
        return $this->render('front/tricks-details.html.twig', [
            'title'             => $trick->getTitle(),
            'firstMedia'        => $libraryRepository->findBy(array('trick' => $trick->getId(), 'type' => Constantes::LIBRARY_IMAGE), array('id'=> 'ASC'), 1, 0),
            'commentForm'       => $commentController->newComment($trick, $request, $manager),
            'trick'             => $trick,
            'pagination'        => $paginator->paginate($trick->getComments(), $request->query->getInt('page', 1), 4)
        ]);
    }

    /**
     * @Route("/front/new", name="add_trick")
     *
     * @param TrickRepository $repository
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return string
     * @throws NonUniqueResultException
     */
    public function newTrick(TrickRepository $repository, Request $request, EntityManagerInterface $manager)
    {
        $trick = new Trick();
        $user = $this->getUser();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        $serviceSlug = new Slugify();
        $title = $form->get('title')->getData();
        $slug = $serviceSlug->generateSlug($title);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setTitle($title);
            $trick->setSlug($slug);
            $trick->setCreatedAt(new \DateTime());
            $trick->setUser($user);
            $result = $repository->findOneBy(['title' => $title]);
            if (!empty($result) && $trick->getId() !== $result->getId()) {
                $this->addFlash('danger', "Le trick existe déjà !");
                return $this->redirectToRoute('add_trick');
            }
            $this->addFlash('light', "Le trick a été créé avec succès !");

            $manager->persist($trick);
            $manager->flush();

            //ajout des vidéos à la collection
            $newVideos = $form->get('videos')->getData();
            foreach ($newVideos as $video) {
                $this->addVideos($video, $trick, $manager);
            }

            /*$files = $form->get('image')->getData();
            if (!empty($files)) {
                $library->setLien($this->uploader($files));
                $library->setType(Constantes::LIBRARY_IMAGE);
                $id_trick = $repository->find($trick->getId());
                $library->setTrick($id_trick);

                $manager->persist($library);
                $manager->flush();
            }*/


            /*$videos = $form->get('videos')->getData();
            $this->addVideos($videos, $trick, $library, $repository, $manager);*/

            return $this->redirectToRoute('home');
        }

        return $this->render('front/tricks-form.html.twig', [
            'title'             => "Ajouter un trick.",
            'formTrick'         => $form->createView(),
            'mediasFormTitle'   => 'Ajouter / modifier / supprimer un média',
            'videosFormTitle'   => 'Vidéos',
            'imgFormTitle'      => 'Images',
            'editMode'          => $trick->getId() !== null
        ]);
    }

    /**
     * @Route("/trick/{slug}/edit", name="edit_trick")
     *
     * @param Trick|null $trick
     * @param TrickRepository $repository
     * @param UserRepository $repo
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return string
     * @throws NonUniqueResultException
     */
    public function editTrick(Trick $trick, TrickRepository $repository, TrickLibraryRepository $libraryRepository, UserRepository $repo, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(TrickType::class, $trick);
        $videos = $libraryRepository->findBy(array('trick' => $trick->getId(), 'type' => Constantes::LIBRARY_VIDEO), array('id'=> 'ASC'));
        $form->get('videos')->setData($videos);

        //TODO::faire les images
        //$images = $libraryRepository->findBy(array('trick' => $trick->getId(), 'type' => Constantes::LIBRARY_IMAGE), array('id'=> 'ASC'));
        //$form->get('images')->setData($images);
        //$newImages = $form->get('images')->getData();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $serviceSlug = new Slugify();
            $title = $form->get('title')->getData();
            $slug = $serviceSlug->generateSlug($title);

            $trick->setTitle($title)
                  ->setSlug($slug);

            //vérification de l'unicité
            $result = $repository->findOneBy(['slug' => $slug]);
            if (!empty($result) && $trick->getId() !== $result->getId()) {
                $this->addFlash('danger', "Le trick existe déjà !");
                return $this->redirectToRoute('edit_trick', array('slug'=> $trick->getSlug()));
            }

            //ajout des vidéos à la collection
            $newVideos = $form->get('videos')->getData();
            foreach ($newVideos as $video) {
                $this->addVideos($video, $trick, $manager);
            }

            //ajout date de modification => envoi email si contributeur != auteur
            $trickHistory = new TrickHistory();
            $user = $this->getUser();
            $author = $repo->findOneByCriteria("username", $trick->getUser());
            $this->addHistory($trick, $trickHistory, $user, $author, $manager);

            $this->addFlash('light', "Le trick a été modifié avec succès !");

            $manager->persist($trick);
            $manager->flush();

/*            $files = $form->get('image')->getData();
            if (!empty($files)) {
                $library->setLien($this->uploader($files));
                $library->setType(Constantes::LIBRARY_IMAGE);$id_trick = $repository->find($trick->getId());
                $library->setTrick($id_trick);

                $manager->persist($library);
                $manager->flush();
            }*/

            return $this->redirectToRoute('home');

        }

        return $this->render('front/tricks-form.html.twig', [
            'title'             => $trick->getTitle(),
            'formTrick'         => $form->createView(),
            'mediasFormTitle'   => 'Ajouter / modifier / supprimer un média',
            'videosFormTitle'   => 'Vidéos',
            'imgFormTitle'      => 'Images',
            'editMode'          => $trick->getId() !== null
        ]);
    }

    /**
     * @deprecated => addVideos & addImages
     * @Route("/trick/{slug}/add/media/", name="add_media")
     * @Route("/trick/{slug}/edit/media/{id_media}", name="edit_media")
     * @param Request $request
     * @param TrickLibraryRepository $repository
     * @param Trick $trick
     * @param EntityManagerInterface $manager
     * @return RedirectResponse
     */
    public function addMedia(Request $request, TrickLibraryRepository $repository, Trick $trick, EntityManagerInterface $manager)
    {
        if (!$trick) {
            $this->addFlash('danger', "Vous n'avez pas sélectionné de trick !");
        }

        $link = null;
        $library = new TrickLibrary();
        if (!empty($request->get('library_id'))) {
            $library = $repository->findOneBy(array("id" => $request->get("library_id")));
        }
        if ($request->get('type') == Constantes::LIBRARY_IMAGE || (empty($request->get('type')))) {
            $file = $request->files->get('file');
            $link = $this->uploader($file);
            $library->setLien($link);
            $library->setType(Constantes::LIBRARY_IMAGE);

        } else if ($request->get('type') == Constantes::LIBRARY_VIDEO) {
            $link = $request->get('lien');
            $library->setLien($link);
            $library->setType($request->get('type'));

        } else {
            $this->addFlash('danger', "Aucune image n'a été entrée !");
        }

        $library->setTrick($trick);
        $manager->persist($library);
        $manager->flush();
        $this->addFlash('light', 'Le média a bien été ajouté !');
        return $this->redirectToRoute('trick_detail', array('slug' => $trick->getSlug()));
    }

    /**
     * Upload picture
     *
     * @param $file
     * @return string
     */
    protected function uploader($file)
    {
        $listFiles = new FilesystemIterator('img/tricks', FilesystemIterator::SKIP_DOTS);
        $count = iterator_count($listFiles);
        $newFileId = $count + 1;
        $newFileName = 'snowtricks-' . $newFileId . '.' . $file->guessExtension();

        try {
            $file->move($this->getParameter('imgTricks_directory'), $newFileName);
        } catch (FileException $e) {
            $this->addFlash('danger', "Un problème est survenu lors du téléchargement de l'image.");
        }
        return $newFileName;
    }

    /**
     * @param $video
     * @param $trick
     * @param $manager
     */
    protected function addVideos($video, $trick, $manager)
    {
        if ($video->getLien())
        {
            $video->setTrick($trick);
            $manager->persist($video);
            $manager->flush();
        }
    }

    /**
     * @param $trick
     * @param $trickHistory
     * @param $user
     * @param $author
     * @param $manager
     */
    protected function addHistory($trick, $trickHistory, $user, $author, $manager)
    {
        $trickHistory->setUser($user)
                    ->setModifiedAt(new \DateTime())
                    ->setTrick($trick);

        $manager->persist($trickHistory);
        $manager->flush();

        if ($user->getId() !== $author->getId()) {
            $serviceMail = new SendMail();
            $serviceMail->alertAuthor($author, $trick);
        }
    }

    /**
     * Delete media picture on library trick
     *
     * @Route("/delete/media/{id}", name="delete_media")
     * @param TrickLibrary $library
     * @param EntityManagerInterface $manager
     * @return RedirectResponse
     */
    public function deleteMedia(TrickLibrary $library, EntityManagerInterface $manager)
    {
        if (!$library){
            $this->addFlash('danger', "Aucune image n'a été séléctionnée.");
        }
        $slug = $library->getTrick()->getSlug();
        $manager->remove($library);
        $manager->flush();
        return $this->redirectToRoute('trick_detail', array('slug' => $slug));
    }

    /**
     * @Route("/delete/trick/{id}", name="delete_trick")
     * @Route("/admin/{id}/delete_trick", name="admin_delete_trick")
     * @param Trick $trick
     * @param EntityManagerInterface $manager
     * @return RedirectResponse
     */
    public function deleteTrick(Trick $trick, EntityManagerInterface $manager)
    {
        $manager->remove($trick);
        $manager->flush();
        $this->addFlash('success', 'Le trick a bien été supprimé !');
        return $this->redirectToRoute('home');
    }
}
