<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\CompleteType;
use App\Form\Type\RegisterType;
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
    public function register(Request $request)
    {
        $form = $this->createForm(RegisterType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Account created successfully');
            /** @var User $user */
            $user = $form->getData();
            $user->setIpAddress($request->getClientIp());
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
            $request->getSession()->set('just_created_user_email', null);
            $this->addFlash('success', 'Account saved successfully. Please, check your mail and click on the link to activate your account.');
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
     * @Route("/auth/reset_password", name="app_auth_reset_password")
     */
    public function resetPassword()
    {
    }
}
