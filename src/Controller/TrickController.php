<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\TrickLibrary;
use App\Form\TrickType;
use App\Framework\Constantes;
use App\Repository\TrickLibraryRepository;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use App\Service\ImagesHelper;
use App\Service\Slugify;
use App\Service\TrickHelper;
use App\Service\VideoHelper;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
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
     * @param FileUploader $fileUploader
     * @return RedirectResponse|Response
     */
    public function newTrick(TrickRepository $repository, Request $request, EntityManagerInterface $manager, FileUploader $fileUploader)
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
            $videoHelper = new VideoHelper($trick, $manager);
            $newVideos = $form->get('videos')->getData();
            foreach ($newVideos as $video) {
                $videoHelper->addVideos($video);
            }

            //ajout d'images à la collection
            $imageHelper = new ImagesHelper($trick, $manager);
            $newImages = $form->get('images')->getData();
            foreach ($newImages as $image) {
                $imageHelper->addImages($image, $fileUploader);
            }
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
     * @param Trick $trick
     * @param TrickRepository $repository
     * @param TrickLibraryRepository $libraryRepository
     * @param UserRepository $repo
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param FileUploader $fileUploader
     * @return RedirectResponse|Response
     */
    public function editTrick(Trick $trick, TrickRepository $repository, TrickLibraryRepository $libraryRepository, UserRepository $repo, Request $request, EntityManagerInterface $manager, FileUploader $fileUploader)
    {
        $form = $this->createForm(TrickType::class, $trick);
        $videos = $libraryRepository->findBy(array('trick' => $trick->getId(), 'type' => Constantes::LIBRARY_VIDEO), array('id'=> 'ASC'));
        $form->get('videos')->setData($videos);
        $images = $libraryRepository->findBy(array('trick' => $trick->getId(), 'type' => Constantes::LIBRARY_IMAGE), array('id'=> 'ASC'));
        $form->get('images')->setData($images);
        dump($form->get('images'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dump($form);
            $serviceSlug = new Slugify();
            $title = $form->get('title')->getData();
            $slug = $serviceSlug->generateSlug($title);
            $trick->setTitle($title)
                ->setSlug($slug);

            //vérification de l'unicité
            $result = $repository->findOneBy(['slug' => $slug]);
            if (!empty($result) && $trick->getId() !== $result->getId()) {
                $this->addFlash('danger', "Le trick existe déjà !");
                return $this->redirectToRoute('edit_trick', array('slug' => $trick->getSlug()));
            }

            //reset et ajout des vidéos à la collection
            $videoHelper = new VideoHelper($trick, $manager);
            $newVideos = $form->get('videos')->getData();
            $videoHelper->deleteVideos($videos);
            foreach ($newVideos as $video) {
                $videoHelper->addVideos($video);
            }

            //reset et ajout des images à la collection
            $imageHelper = new ImagesHelper($trick, $manager);
            $newImages = $form->get('images')->getData();
            dump($newImages);
            //$imageHelper->deleteImages($images);
            foreach ($newImages as $image) {
                if ($image->getLien()) {
                    dump($image);
                    $imageHelper->addImages($image, $fileUploader);
                }
            }

            //ajout date de modification => envoi email si contributeur != auteur
            $user = $this->getUser();
            $trickHelper = new TrickHelper($trick, $repo, $manager);
            $trickHelper->addModifiedBy($user);

            $this->addFlash('light', "Le trick a été modifié avec succès !");
            $manager->persist($trick);
            $manager->flush();
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

    /**
     * @Route("delete/media/{id}", name="delete_media")
     *
     * @param TrickLibrary $library
     * @param EntityManagerInterface $manager
     */
    public function deleteMedia(TrickLibrary $library, TrickRepository $repository, EntityManagerInterface $manager)
    {
        $trick = $repository->findOneBy(array('id' => $library->getTrick()));
        $manager->remove($library);
        $manager->flush();
        $this->addFlash('success', 'Le média a bien été supprimé !');
        return $this->redirectToRoute('trick_detail', array('slug'=> $trick->getSlug()));
    }
}
