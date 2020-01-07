<?php

namespace App\Controller;

use App\Account\Registerer;
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
use \Symfony\Component\HttpFoundation\RedirectResponse;

class AuthController extends AbstractController
{
    /**
     * @Route("/login", name="app_auth_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', [
            'user' => null,
            'is_home' => false,
            'breadcrumbs' => [],
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/register", name="app_auth_register")
     * @param Request $request
     * @return Response
     */
    public function register(Request $request, Registerer $registerer)
    {
        $form = $this->createForm(RegisterType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'messages.auth.created');
            /** @var User $user */
            $user = $form->getData();
            $registerer->register($user, $request->getClientIp());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
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
     * @Route("/activate/{code}", name="app_auth_activate", requirements={"code"=".{10,50}"})
     * @param string $code
     * @return RedirectResponse
     */
    public function activate(string $code)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['activationCode' => $code]);
        if (!$user) {
            throw new NotFoundHttpException();
        }
        $this->addFlash('success', 'messages.auth.activated');
        /** @var User $user */
        $user->setActive(true);
        $user->setActivationCode(null);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_auth_login');
    }

    /**
     * @Route("/complete", name="app_auth_complete")
     * @param Request $request
     * @return Response
     */
    public function complete(Request $request)
    {
        $repo = $this->getDoctrine()->getRepository(User::class);
        $email = $request->getSession()->get('just_created_user_email');
        if (!$email) {
            throw new NotFoundHttpException();
        }
        $user = $repo->findOneBy(['email' => $email]);
        if (!$user) {
            throw new NotFoundHttpException();
        }
        $form = $this->createForm(CompleteType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'messages.auth.completed');
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($form->getData());
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
     * @Route("/ask_reset_password", name="app_auth_ask_reset_password")
     * @param Request $request
     * @param PasswordResetTokenGenerator $tokenGenerator
     * @return Response
     */
    public function askPasswordReset(Request $request, PasswordResetTokenGenerator $tokenGenerator)
    {
        $form = $this->createForm(AskPasswordResetType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $tokenGenerator->treatEmail($form->getData()['email']);
            $this->addFlash('success', 'messages.auth.reset_sent');
            
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
     * @Route("/reset_password/{code}", name="app_auth_reset_password", requirements={"code"=".{10,50}"})
     * @param Request $request
     * @param string $code
     * @return RedirectResponse|Response
     */
    public function resetPassword(Request $request, string $code)
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
            $this->addFlash('success', 'messages.auth.password_changed');
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
