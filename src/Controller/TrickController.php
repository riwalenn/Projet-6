<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Comment;
use App\Entity\TrickHistory;
use App\Entity\TrickLibrary;
use App\Form\TrickLibraryType;
use App\Form\TrickType;
use App\Repository\CommentRepository;
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
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param TrickLibraryRepository $libraryRepository
     * @param CommentRepository $commentRepository
     * @return RedirectResponse|Response
     */
    public function show(Trick $trick, PaginatorInterface $paginator, Request $request, EntityManagerInterface $manager, TrickLibraryRepository $libraryRepository, CommentRepository $commentRepository)
    {
        $firstMedia = $libraryRepository->findBy(array('trick' => $trick->getId(), 'type' => 1), array('id'=> 'ASC'), 1, 0);
        $comments = $commentRepository->findBy(array('Trick' => $trick->getId()), array('created_at' => 'DESC'));
        $pagination = $paginator->paginate(
            $comments,
            $request->query->getInt('page', 1),
            4
        );
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



            return $this->redirectToRoute('trick_detail', ['slug' => $trick->getSlug()]);
        }

        return $this->render('front/tricks-details.html.twig', [
            'title'             => $trick->getTitle(),
            'firstMedia'        => $firstMedia,
            'trick'             => $trick,
            'pagination'        => $pagination,
            'commentForm'       => $form->createView()
        ]);
    }

    /**
     * More medias on trick
     *
     * @Route("/trick/{id}/{offset}", name="load_medias", requirements={"offset": "\d+"})
     * @param Trick $trick
     * @param TrickLibraryRepository $libraryRepository
     * @param int $offset
     * @return Response
     */
    public function loadMoreMedias(Trick $trick, TrickLibraryRepository $libraryRepository, $offset = 6)
    {
        $itemsLibrary = $libraryRepository->findBy(array('trick' => $trick->getId()), array(), 3, $offset);

        return $this->render('front/medias-more.html.twig', [
            'itemsLibrary' => $itemsLibrary,
            'trick' => $trick
        ]);
    }

    /**
     * @Route("/front/new", name="add_trick")
     *
     * @param TrickRepository $repository
     * @param UserRepository $userRepository
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return string
     * @throws NonUniqueResultException
     */
    public function newTrick(TrickRepository $repository, UserRepository $userRepository, Request $request, EntityManagerInterface $manager)
    {
        $trick = new Trick();
        $library = new TrickLibrary();
        $user = $this->getUser();
        $form = $this->createForm(TrickType::class, $trick);
        $formTrick = $this->createForm(TrickLibraryType::class);
        $formTrick->handleRequest($request);
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

            $files = $form->get('image')->getData();
            if (!empty($files)) {
                $library->setLien($this->uploader($files));
                $library->setType(1);$id_trick = $repository->find($trick->getId());
                $library->setTrick($id_trick);

                $manager->persist($library);
                $manager->flush();
            }

            return $this->redirectToRoute('home');
        }

        return $this->render('front/tricks-form.html.twig', [
            'title'         => "Ajouter un trick.",
            'formTrick'     => $form->createView(),
            'formLibrary'     => $formTrick->createView(),
            'editMode'      => $trick->getId() !== null
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
    public function editTrick(Trick $trick, TrickRepository $repository, UserRepository $repo, Request $request, EntityManagerInterface $manager)
    {
        $library = new TrickLibrary();
        $user = $this->getUser();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
        $serviceSlug = new Slugify();
        $title = $form->get('title')->getData();
        $slug = $serviceSlug->generateSlug($title);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setTitle($title);
            $trick->setSlug($slug);
            $id_trick = $repository->find($trick->getId());
            $library->setTrick($id_trick);
            $author = $repo->findOneByCriteria("username", $trick->getUser());
            $result = $repository->findOneBy(['slug' => $slug]);
            if (!empty($result) && $trick->getId() !== $result->getId()) {
                $this->addFlash('danger', "Le trick existe déjà !");
                return $this->redirectToRoute('edit_trick', array('slug'=> $trick->getSlug()));
            }
            if ($user->getId() !== $author->getId()) {
                $trickHistory = new TrickHistory();
                $trickHistory->setUser($user)
                    ->setTrick($trick)
                    ->setModifiedAt(new \DateTime());
                $manager->persist($trickHistory);
                $serviceMail = new SendMail();
                $serviceMail->alertAuthor($author, $trick);
            }
            $this->addFlash('light', "Le trick a été modifié avec succès !");

            $manager->persist($trick);
            $manager->flush();

            $files = $form->get('image')->getData();
            if (!empty($files)) {
                $library->setLien($this->uploader($files));
                $library->setType(1);$id_trick = $repository->find($trick->getId());
                $library->setTrick($id_trick);

                $manager->persist($library);
                $manager->flush();
            }

            return $this->redirectToRoute('home');

        }

        return $this->render('front/tricks-form.html.twig', [
            'title'         => $trick->getTitle(),
            'formTrick'     => $form->createView(),
            'editMode'      => $trick->getId() !== null
        ]);
    }

    /**
     * @Route("/trick/{id}/add/media/", name="add_media")
     * @Route("/trick/{id}/edit/media/{id_media}", name="edit_media")
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
        if ($request->get('type') == 1 || (empty($request->get('type')))) {
            $file = $request->files->get('file');
            $link = $this->uploader($file);
            $library->setLien($link);
            $library->setType(1);

        } else if ($request->get('type') == 2) {
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
