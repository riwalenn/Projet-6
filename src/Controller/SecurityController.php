<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use App\Service\SendMail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="security_registration")
     */
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, UserRepository $userRepository)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //$imageArray = array_diff(scandir('img/profils'), array('..', '.'));
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $user->setToken(bin2hex(random_bytes(32)));
            //$user->setImage(array_rand(array_flip($imageArray)));
            $user->setImage(mt_rand(1, 9));
            $user->setCreatedAt(new \DateTime());
            $user->setIsActive(0);

            $manager->persist($user);
            $manager->flush();

            $serviceMail = new SendMail();
            $userEmail = $userRepository->findOneBy(array("email" => $user->getEmail()));
            //$userEmail = $userRepository->findOneByCriteria('email', $user->getEmail());
            $serviceMail->sendToken($userEmail, 'inscription');

            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView(),
            'title' => "S'inscrire sur SnowTricks"
        ]);
    }

    /**
     * @Route("/confirmation", name="confirmation_registration")
     */
    public function confirmation(UserRepository $userRepository, Request $request, EntityManagerInterface $manager)
    {
        $token = $request->query->get('token');
        $user = $userRepository->findOneBy(array("token" => $token));
        if (empty($user)) {
            $this->addFlash('light', "Votre token n'existe pas");
            return $this->redirectToRoute('home');
        } else {
            $dateDiff = (new \DateTime())->diff($user->getCreatedAt())->days;
        }

        //$createdAt = null;

        if ($dateDiff < 15) {
            $user->setIsActive(1);
            /* $user->setCreatedAt($createdAt);
             $user->setToken(null);*/
            $user->setToken(''); //Pourquoi je n'arrive pas à mettre le token et la date à null !
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('light', "Votre compte a été activé avec succès !");

            return $this->redirectToRoute('home');
        } else {
            $manager->remove($user);
            $manager->flush();
            $this->addFlash('danger', "Votre token a expiré ! Veuillez vous réinscrire.");
            return $this->redirectToRoute('security_registration');
        }
    }

    /**
     * @Route("/connexion", name="security_login")
     */
    public function login()
    {
        return $this->render('security/login.html.twig', [
            'title' => "Connectez-vous !"
        ]);
    }

    /**
     * @Route("/deconnexion", name="security_logout")
     */
    public function logout()
    {
    }
}
