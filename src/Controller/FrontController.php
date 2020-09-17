<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\TrickHistory;
use App\Entity\User;
use App\Repository\TrickHistoryRepository;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FilesystemIterator;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;

class FrontController extends AbstractController
{
    private $title = "Bienvenue sur le site communautaire de SnowTricks !";


    /**
     * @Route("/", name="home")
     */
    public function home(TrickRepository $repository, UserRepository $userRepository)
    {
        $tricks = $repository->findAll();
        return $this->render('front/home.html.twig', [
            'controller_name' => 'FrontController',
            'title' => $this->title,
            'tricks' => $tricks]);
    }

    /**
     * @Route("/tricks_detail/{id}", name="trick_detail")
     */
    public function tricks_detail(Trick $trick, TrickHistoryRepository $historyRepository)
    {
        $trick_history = $historyRepository->findAll();
        return $this->render('front/tricks-details.html.twig', [
            'title' => "Tricks",
            'trick' => $trick,
            'trick_history' => $trick_history
        ]);
    }

    /**
     * @Route("/delete_trick/{id}", name="delete_trick")
     */
    public function deleteTrick(Trick $trick, EntityManagerInterface $manager)
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

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/front/newTrick", name="add_trick")
     * @Route("/front/{id}/edit", name="edit_trick")
     *
     * @return string
     */
    public function formTrick(Trick $trick = null, Request $request, EntityManagerInterface $manager)
    {
        if (!$trick) {
            $trick = new Trick();
        }

        $user = $this->getUser();

        $form = $this->createFormBuilder($trick)
                        ->add('title')
                        ->add('description')
                        ->add('position')
                        ->add('grabs')
                        ->add('rotation')
                        ->add('flip')
                        ->add('slide')
                        ->add('image', FileType::class, [
                            'label' => 'snowtricks-',
                            'mapped' => false,
                            'required' => false,
                            'constraints' => [
                                new File([
                                    'maxSize' => '1024k',
                                    'mimeTypes' => [
                                        'image/jpeg',
                                    ],
                                    'mimeTypesMessage' => 'Merci d\'upload un fichier jpeg',
                                ])
                            ],
                        ])
                        ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$trick->getId()) {
                $trick->setCreatedAt(new \DateTime());
                $trick->setUser($user);
                $files = $form->get('image')->getData();
                if ($files) {
                    $listFiles = new FilesystemIterator('img/tricks', FilesystemIterator::SKIP_DOTS);
                    $count = iterator_count($listFiles);
                    $newFileId = $count+1;

                    $newFileName = 'snowtricks-'. $newFileId .'.'. $files->guessExtension();

                    try {
                        $files->move($this->getParameter('imgTricks_directory'), $newFileName);
                    }catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    $trick->setImage($newFileName);
                }
            }
            $manager->persist($trick);
            $manager->flush();

            return $this->redirectToRoute('home');
        }

        if ($trick) {
            $title = $trick->getTitle();
        } else {
            $title = "Ajouter un trick.";
        }

        return $this->render('front/tricks-form.html.twig', [
            'title' => $title,
            'formTrick' => $form->createView(),
            'editMode' => $trick->getId() !== null
        ]);
    }
}
