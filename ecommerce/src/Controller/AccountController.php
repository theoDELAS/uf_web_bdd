<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Entity\User;
use App\Form\AccountType;
use App\Form\PasswordUpdateType;
use App\Form\RegistrationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccountController extends AbstractController
{
    /**
     * Permet d'afficher et de gérer le formulaire de connexion
     *
     * @Route("/login", name="account_login")
     *
     * @param AuthenticationUtils $utils
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        return $this->render('account/login.html.twig', [
            'hasError' => $error !== null,
            'username' => $username
        ]);
    }

    /**
     * Permet de se déconnecter
     *
     * @Route("/logout", name="account_logout")
     *
     * @return void
     */
    public function logout()
    {
    }

    /**
     * Permet d'afficher le formulaire d'inscription
     *
     * @Route("/register", name="account_register")
     *
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user =  new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $hash = $encoder->encodePassword($user, $user->getHash());
            $user->setHash($hash);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre compte a bien été créé. Vous pouvez maintenant vous connectez"
            );

            return $this->redirectToRoute('account_login');
        }

        return $this->render('account/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher et de traiter le formulaire de modification de profil
     *
     * @Route("/account/profile", name="account_profile")
     * @IsGranted("ROLE_USER")
     *
     * @param Request $request
     * @return Response
     */
    public function profile(Request $request)
    {
        $user = $this->getUser();

        $form = $this->createForm(AccountType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les données du profil ont bien été modifiées"
            );

            return $this->redirectToRoute('account_index');
        }

        return $this->render('account/profile.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * Permet de modifier le mot de passe
     *
     * @Route("/account/password-update", name="account_password")
     * @IsGranted("ROLE_USER")
     *
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $passwordUpdate = new PasswordUpdate();

        $user = $this->getUser();

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            // Vérifier que l'ancien password du formulaire soit le meme que le password de l'user
            if(!password_verify($passwordUpdate->getOldPassword(), $user->getHash()))
            {
                // Gérer erreur
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe actuel"));
            }
            else
            {
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user, $newPassword);

                $user->setHash($hash);

                $manager = $this->getDoctrine()->getManager();
                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "Votre mot de passe a bien été modifié"
                );
                return $this->redirectToRoute('home');
            }
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     *Permet d'afficher le profil de l'user connecté
     *
     * @Route("/account", name="account_index")
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function myAccount()
    {
        return $this->render('user/index.html.twig', [
            'user' => $this->getUser()
        ]);
    }
}
