<?php

namespace App\Controller;

use App\Form\Type\AccountType;
use App\Form\Type\CompleteType;
use App\Form\Type\PasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AccountController extends AbstractController
{
    /**
     * @Route("/account/complete", name="app_account_complete")
     */
    public function complete()
    {
        $form = $this->createForm(CompleteType::class);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Account saved successfully');
            $user = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_about_home');
        }

        return $this->render(
            'account/complete.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/account", name="app_account_myself")
     */
    public function myself(Request $request, TokenStorageInterface $tokenStorage)
    {
        $user = $tokenStorage->getToken()->getUser();
        $accountForm = $this->createForm(AccountType::class, $user);
        $passwordForm = $this->createForm(PasswordType::class, $user);
        
        $accountForm->handleRequest($request);
        $passwordForm->handleRequest($request);
        $submitedUser = null;
        if ($accountForm->isSubmitted() && $accountForm->isValid()) {
            $this->addFlash('success', 'Account saved successfully');
            $submitedUser = $accountForm->getData();
        }
        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $this->addFlash('success', 'Password changed successfully');
            $submitedUser = $passwordForm->getData();
        }
        if ($submitedUser) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($submitedUser);
            $entityManager->flush();

            return $this->redirectToRoute('app_account_myself');
        }
        
        return $this->render(
            'account/edit.html.twig',
            [
                'user' => $user,
                'accountForm' => $accountForm->createView(),
                'passwordForm' => $passwordForm->createView(),
            ]
        );
    }
}
