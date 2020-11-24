<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ForgotPasswordType;
use App\Form\PasswordRecoveryType;
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
            'form'      => $form->createView(),
            'title'     => "S'inscrire sur SnowTricks"
        ]);
    }

    /**
     * @Route("/iForgotMyPassword", name="change_password")
     */
    public function change_password(UserRepository $userRepository, Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager)
    {
        $token = $request->query->get('token');
        $user = $userRepository->findOneBy(array("token" => $token));
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (empty($user)) {
                $this->addFlash('error', "Votre token n'existe pas");
                return $this->redirectToRoute('home');
            } else {
                $dateDiff = (new \DateTime())->diff($user->getCreatedAt())->days;
                if ($dateDiff < 2 && $user->getIsActive() == 1) {
                    $data = $form->getData();
                    $hash = $encoder->encodePassword($user, $data['password']);
                    $user->setPassword($hash);
                    $manager->persist($user);
                    $manager->flush();
                    $this->addFlash('success', "Votre mot de passe a été modifié avec succès !");

                } else {
                    $this->addFlash('error', "Votre token a expiré !");
                    return $this->redirectToRoute('email_form');
                }
            }
        }
        return $this->render('security/password-change.html.twig', [
            'form'      => $form->createView(),
            'title'     => "J'ai oublié mon mot de passe !"
        ]);
    }

    /**
     * @Route("/confirmation", name="confirmation_registration")
     */
    public function confirmation(UserRepository $userRepository, Request $request, EntityManagerInterface $manager)
    {
        $token = $request->query->get('token');
        $user = $userRepository->findOneBy(array("token" => $token));
        if (empty($user) || empty($token)) {
            $this->addFlash('error', "Votre token n'existe pas");
            return $this->redirectToRoute('security_registration');
        } else {
            $dateDiff = (new \DateTime())->diff($user->getCreatedAt())->days;
        }

        if ($dateDiff < 3) {
            $user->setIsActive(1);
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
     * @Route("/emailForm", name="email_form")
     */
    public function forgot_password(Request $request, EntityManagerInterface $manager, UserRepository $userRepository)
    {
        $form = $this->createForm(PasswordRecoveryType::class);
        $form->handleRequest($request);
        $email = $form->get('email')->getData();
        $user = $userRepository->findOneBy(array("email" => $email));
        if ($form->isSubmitted() && $form->isValid()) {
            if (empty($user)) {
                $this->addFlash('error', "Votre email n'est pas reconnu dans notre base.");
                return $this->redirectToRoute('security_registration');
            }
            $user->setToken(bin2hex(random_bytes(32)));
            $user->setCreatedAt(new \DateTime());

            $manager->flush();
            $manager->persist($user);

            $serviceMail = new SendMail();
            $serviceMail->sendToken($user, 'oubli');

            $this->addFlash('success', "Un email vous a été envoyé !");
        }

        return $this->render('security/password-forgotten.html.twig', [
            'form'      => $form->createView(),
            'title'     => "J'ai oublié mon mot de passe !"
        ]);
    }

    /**
     * @Route("/connexion", name="security_login")
     */
    public function login()
    {
        return $this->render('security/login.html.twig', [
            'title'     => "Connectez-vous !"
        ]);
    }

    /**
     * @Route("/deconnexion", name="security_logout")
     */
    public function logout()
    {
    }
}
