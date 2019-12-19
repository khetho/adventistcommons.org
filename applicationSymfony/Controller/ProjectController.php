<?php

namespace App\Controller;

use App\Project\Form\Type\AddMemberType;
use App\Project\Form\Type\DeleteType;
use App\Twig\RoutesExtension;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    /**
     * @Route("/{slug}/{languageCode}", name="app_project_show")
     * @param Request $request
     * @param $slug
     * @param $languageCode
     * @param DataFinder $dataFinder
     * @param RoutesExtension $routesMaker
     * @return Response
     */
    public function show(Request $request, $slug, $languageCode, DataFinder $dataFinder, RoutesExtension $routesMaker)
    {
        $project = $dataFinder->retrieveProjectOr404($slug, $languageCode);
        $addUserForm = $this->createForm(AddMemberType::class);
        $addUserForm->handleRequest($request);
        if ($addUserForm->isSubmitted() && $addUserForm->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $project->addMember($addUserForm->getData()['user']);
            $manager->persist($project);
            $manager->flush();
            $this->addFlash('success', 'Member successfully added');

            return $this->redirect($routesMaker->pathToProject($project));
        }

        return $this->render('project/show.html.twig', [
            'addMemberForm' => $addUserForm->createView(),
            'project' => $project,
        ]);
    }

    /**
     * @Route("/{slug}/{languageCode}/delete", name="app_project_delete")
     * @param Request $request
     * @param $slug
     * @param $languageCode
     * @param DataFinder $dataFinder
     * @return Response
     */
    public function delete(Request $request, $slug, $languageCode, DataFinder $dataFinder)
    {
        $project = $dataFinder->retrieveProjectOr404($slug, $languageCode);
        $deleteForm = $this->createForm(DeleteType::class, $project);
        $deleteForm->handleRequest($request);
        if ($deleteForm->isSubmitted() && $deleteForm->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->remove($deleteForm->getData());
            $manager->flush();
            $this->addFlash('success', 'Project successfully deleted');

            return $this->redirectToRoute('app_project_list');
        }

        return $this->render('project/delete.html.twig', [
            'project' => $project,
            'deleteForm' => $deleteForm->createView(),
        ]);
    }
}
