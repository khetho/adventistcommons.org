<?php

namespace App\Controller;

use App\Entity\ContentRevision;
use App\Entity\Project;
use App\Entity\User;
use App\Form\Type\AccountType;
use App\Form\Type\CompleteType;
use App\Form\Type\DeleteAccountType;
use App\Form\Type\PasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use \Symfony\Component\HttpFoundation\Response;

class AccountController extends AbstractController
{
    /**
     * @Route("/account/complete", name="app_account_complete")
     * @param Request $request
     * @return Response
     */
    public function complete(Request $request)
    {
        $form = $this->createForm(CompleteType::class, $this->getUser());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'messages.account.completed');
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
     * @param Request $request
     * @return Response
     */
    public function myself(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $modifiedUser = null;

        $accountForm = $this->createForm(AccountType::class, $user);
        $accountForm->handleRequest($request);
        if ($accountForm->isSubmitted() && $accountForm->isValid()) {
            $this->addFlash('success', 'messages.account.saved');
            $modifiedUser = $accountForm->getData();
        }

        $passwordForm = $this->createForm(PasswordType::class);
        $passwordForm->handleRequest($request);
        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $this->addFlash('success', 'messages.account.password_saved');
            $password = $passwordForm->getData();
            $modifiedUser = $user;
            $modifiedUser->setPlainPassword($password->getNewPassword());
        }

        $deleteForm = $this->createForm(DeleteAccountType::class, $user);
        $deleteForm->handleRequest($request);
        if ($deleteForm->isSubmitted() && $deleteForm->isValid()) {
            $
            $this->addFlash('success', 'messages.account.removed');
            $modifiedUser = $user->forget();
            $redirectRoute = 'app_auth_logout';
        }

        if ($modifiedUser) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($modifiedUser);
            $entityManager->flush();

            return $this->redirectToRoute($redirectRoute ?? 'app_account_myself');
        }

        // translations data
        $projectRepo = $this->getDoctrine()->getRepository(Project::class);
        $contentRepo = $this->getDoctrine()->getRepository(ContentRevision::class);

        $translatedProjects = $projectRepo->findQueryForTranslator($user)->setMaxResults(10)->getResult();
        $untranslatedProjects = $projectRepo->findQueryForUserNotTranslator($user)->setMaxResults(10)->getResult();
        $contributions = $contentRepo->getUserReport($user, 'translator');
        $contribPerMonth = $contentRepo->getUserReportPerMonth($user, 'translator');

        // proofreading data
        $approvedProjects = $projectRepo->findQueryForApprover($user)->setMaxResults(10)->getResult();
        $unapprovedProjects = $projectRepo->findQueryForNotApprover($user)->setMaxResults(10)->getResult();
        $proofrContribs = $contentRepo->getUserReport($user, 'approver');
        $proofrContribPerMnth = $contentRepo->getUserReportPerMonth($user, 'approver');

        // reviewing data
        $reviewedProjects = $projectRepo->findQueryForReviewer($user)->setMaxResults(10)->getResult();
        $unreviewedProjects = $projectRepo->findQueryForNotReviewer($user)->setMaxResults(10)->getResult();

        return $this->render(
            'account/edit.html.twig',
            [
                'user' => $user,
                'accountForm' => $accountForm->createView(),
                'passwordForm' => $passwordForm->createView(),
                'deleteForm' => $deleteForm->createView(),

                'translatedProjects' => $translatedProjects,
                'untranslatedProjects' => $untranslatedProjects,
                'contributions' => $contributions,
                'contributionsPerMonth' => $contribPerMonth,

                'approvedProjects' => $approvedProjects,
                'unapprovedProjects' => $unapprovedProjects,
                'proofreaderContributions' => $proofrContribs,
                'proofreaderContributionsPerMonth' => $proofrContribPerMnth,

                'reviewedProjects' => $reviewedProjects,
                'unreviewedProjects' => $unreviewedProjects,
            ]
        );
    }
}
