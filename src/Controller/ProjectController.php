<?php

namespace App\Controller;

use AdventistCommons\Idml\Entity\Holder;
use App\Product\DownloadLogger;
use App\Product\Idml\Translator;
use App\Project\Form\Type\AddMemberType;
use App\Project\Form\Type\DeleteType;
use App\Twig\RoutesExtension;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    /**
     * @Route("/{slug}/project/{languageCode}", name="app_project_show")
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
            $this->addFlash('success', 'messages.project.member_added');

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
            $this->addFlash('success', 'messages.project.deleted');

            return $this->redirectToRoute('app_project_list');
        }

        return $this->render('project/delete.html.twig', [
            'project' => $project,
            'deleteForm' => $deleteForm->createView(),
        ]);
    }

    /**
     * @Route("/{slug}/{languageCode}/project.idml", name="app_project_download_idml")
     * @param string $slug
     * @param string $languageCode
     * @param Translator $translator
     * @param DataFinder $dataFinder
     * @param DownloadLogger $downloadLogger
     * @return BinaryFileResponse
     * @throws Exception
     */
    public function downloadIdml(
        string $slug,
        string $languageCode,
        Translator $translator,
        DataFinder $dataFinder,
        DownloadLogger $downloadLogger
    ) {
        $project = $dataFinder->retrieveProjectOr404($slug, $languageCode);
        /** @var Holder $holder */
        $holder = $translator->translate($project);
        $downloadLogger->log($project);

        $response = new BinaryFileResponse($holder->getZipFileName());
        $response->headers->set('Content-Type', 'application/zip');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $holder->buildFileName());

        return $response;
    }
}
