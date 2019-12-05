<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\AccountType;
use App\Form\Type\PasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AccountController extends AbstractController
{
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
            $this->addFlash('success', 'Account saved succesfuly');
            $submitedUser = $accountForm->getData();
        }
        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $this->addFlash('success', 'Password changed succesfuly');
            $submitedUser = $passwordForm->getData();
        }
        if ($submitedUser) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($submitedUser);
            $entityManager->flush();

            return $this->redirectToRoute('app_account_myself');
        }
        
        return $this->render(
            'user/account.html.twig',
            [
                'user' => $user,
                'accountForm' => $accountForm->createView(),
                'passwordForm' => $passwordForm->createView(),
            ]
        );
    }
}
