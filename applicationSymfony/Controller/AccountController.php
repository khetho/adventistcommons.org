<?php

namespace App\Controller;

use App\Account\Remover;
use App\Form\Type\AccountType;
use App\Form\Type\CompleteType;
use App\Form\Type\PasswordType;
use App\Form\Type\DeleteAccountType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AccountController extends AbstractController
{
    /**
     * @Route("/account/complete", name="app_account_complete")
     */
    public function complete(Request $request)
    {
        $form = $this->createForm(CompleteType::class, $this->getUser());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Account completed successfully');
            $user = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_about_home');
        }

        return $this->render(
            'account/complete.html.twig',
            [
                'user' => $this->getUser(),
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/account", name="app_account_myself")
     */
    public function myself(Request $request, Remover $userRemover)
    {
        $user = $this->getUser();
        $modifiedUser = null;
        
        $accountForm = $this->createForm(AccountType::class, $user);
        $accountForm->handleRequest($request);
        if ($accountForm->isSubmitted() && $accountForm->isValid()) {
            $this->addFlash('success', 'Account saved successfully');
            $modifiedUser = $accountForm->getData();
        }

        $passwordForm = $this->createForm(PasswordType::class);
        $passwordForm->handleRequest($request);
        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $this->addFlash('success', 'Password changed successfully');
            $password = $passwordForm->getData();
            $modifiedUser = $user;
            $modifiedUser->setPlainPassword($password->getNewPassword());
        }

        $deleteForm = $this->createForm(DeleteAccountType::class, $user);
        $deleteForm->handleRequest($request);
        if ($deleteForm->isSubmitted() && $deleteForm->isValid()) {
            $this->addFlash('success', 'Account removed successfully');
            $modifiedUser = $user;
            $userRemover->cleanupUser($modifiedUser);
            $redirectRoute = 'app_auth_logout';
        }
        
        if ($modifiedUser) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($modifiedUser);
            $entityManager->flush();

            return $this->redirectToRoute($redirectRoute ?? 'app_account_myself');
        }
        
        return $this->render(
            'account/edit.html.twig',
            [
                'user' => $this->getUser(),
                'accountForm' => $accountForm->createView(),
                'passwordForm' => $passwordForm->createView(),
                'deleteForm' => $deleteForm->createView(),
            ]
        );
    }
}
