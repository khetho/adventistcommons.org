<?php

namespace App\Controller;

use AdventistCommons\Basics\StringFunctions;
use App\Entity\User;
use App\Form\Type\CompleteType;
use App\Form\Type\RegisterType;
use App\Form\Type\AskPasswordResetType;
use App\Form\Type\PasswordResetType;
use App\Security\PasswordResetTokenGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{
    /**
     * @Route("/auth/login", name="app_auth_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', [
            'title' => 'Sign in',
            'user' => null,
            'is_home' => false,
            'breadcrumbs' => [],
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/auth/register", name="app_auth_register")
     */
    public function register(Request $request, StringFunctions $stringFunctions)
    {
        $form = $this->createForm(RegisterType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Account created successfully. We sent you a validation email.');
            /** @var User $user */
            $user = $form->getData();
            $user->setIpAddress($request->getClientIp());
            $user->setActive(false);
            $user->setActivationCode($stringFunctions->generateString(25));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            /** @TODO : send email **/
            $request->getSession()->set('just_created_user_email', $user->getEmail());

            return $this->redirectToRoute('app_auth_complete');
        }

        return $this->render(
            'auth/register.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/auth/activate/{code}", name="app_auth_activate", requirements={"code"=".{10,50}"})
     */
    public function activate(Request $request, string $code)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['activationCode' => $code]);
        if (!$user) {
            throw new NotFoundHttpException();
        }
        $this->addFlash('success', 'Account activated successfully. You can now Login.');
        /** @var User $user */
        $user->setActive(true);
        $user->setActivationCode(null);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_auth_login');
    }

    /**
     * @Route("/auth/complete", name="app_auth_complete")
     */
    public function complete(Request $request)
    {
        $repo = $this->getDoctrine()->getRepository(User::class);
        $email = $request->getSession()->get('just_created_user_email');
        if (!$email || !($user = $repo->findOneBy(['email' => $email]))) {
            throw new NotFoundHttpException();
        }
        $form = $this->createForm(CompleteType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
//            $request->getSession()->set('just_created_user_email', null);
            $this->addFlash('success', 'Account created successfully. Please, check your emails and click on the link to activate your account.');
            // @todo : send email
            $user = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_about_home');
        }

        return $this->render(
            'account/complete.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/auth/ask_reset_password", name="app_auth_ask_reset_password")
     */
    public function askPasswordReset(Request $request, PasswordResetTokenGenerator $passwordResetTokenGenerator)
    {
        $form = $this->createForm(AskPasswordResetType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $passwordResetTokenGenerator->treateEmail($form->getData()['email']);
            $this->addFlash('success', 'Password request successfully submitted. Please, check your emails and click on the link to set your password.');
            
            return $this->redirect($this->generateUrl('app_auth_ask_reset_password'));
        }

        return $this->render(
            'auth/askResetPassword.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/auth/reset_password/{code}", name="app_auth_reset_password", requirements={"code"=".{10,50}"})
     */
    public function resetPassword(Request $request, string $code, PasswordResetTokenGenerator $passwordResetTokenGenerator)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['forgottenPasswordCode' => $code]);
        if (!$user) {
            throw new NotFoundHttpException();
        }
        $form = $this->createForm(PasswordResetType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setForgottenPasswordCode(null);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Password successfully changed. You can now login with your new password.');
            return $this->redirect($this->generateUrl('app_auth_login'));
        }

        return $this->render(
            'auth/resetPassword.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
