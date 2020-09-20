<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Comment;
use App\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TrickHistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use FilesystemIterator;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\File;

class TrickController extends AbstractController
{
    private $title = "Bienvenue sur le site communautaire de SnowTricks !";

    /**
     * @Route("/tricks_detail/{id}", name="trick_detail")
     */
    public function tricks_detail(Trick $trick, Request $request, EntityManagerInterface $manager, TrickHistoryRepository $historyRepository)
    {
        $trick_history = $historyRepository->findAll();
        $comment = new Comment();
        /*$form = $this->createForm(CommentType::class, $comment);*/
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
            'title' => "Tricks",
            'trick' => $trick,
            'trick_history' => $trick_history,
            'commentForm' => $form->createView()
        ]);
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
}
