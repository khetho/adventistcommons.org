<?php

namespace App\Controller;

use App\Entity\Language;
use App\Entity\Product;
use App\Entity\Project;
use App\Project\Form\Type\AddType;
use App\Project\Form\Type\DeleteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    /**
     * @Route("/{slug}/{languageCode}", name="app_project_show")
     * @param Request $request
     * @return Response
     */
    public function show($slug, $languageCode, DataFinder $dataFinder)
    {
        $project = $dataFinder->retrieveProjectOr404($slug, $languageCode);

        return $this->render('project/show.html.twig', [
            'project' => $project,
        ]);
    }

    /**
     * @Route("/{slug}/{languageCode}/delete", name="app_project_delete")
     * @param Request $request
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
