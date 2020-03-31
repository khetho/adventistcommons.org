<?php

namespace App\Controller;

use App\Entity\Language;
use App\Entity\Project;
use App\Project\Form\Type\AddType;
use App\Repository\LanguageRepository;
use App\Twig\RoutesExtension;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectsController extends AbstractController
{
    /**
     * @Route("/{slug}/create-project", name="app_project_add")
     * @param Request $request
     * @param string $slug
     * @param DataFinder $dataFinder
     * @param RoutesExtension $routeMaker
     * @return Response
     */
    public function add(Request $request, string $slug, DataFinder $dataFinder, RoutesExtension $routeMaker)
    {
        $product = $dataFinder->retrieveProductOr404($slug);
        $addForm = $this->createForm(AddType::class);
        $addForm->handleRequest($request);
        if ($addForm->isSubmitted() && $addForm->isValid()) {
            $project = $addForm->getData();
            $project->setProduct($product);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($project);
            $entityManager->flush();
            $this->addFlash('success', 'messages.project.created');

            return $this->redirect($routeMaker->pathToProject($project));
        }

        return $this->render('product/add/add.html.twig', [
            'productAddForm' => $addForm->createView(),
        ]);
    }

    /**
     * @Route("/{languageCode}", name="app_project_list", requirements={"languageCode"=".+"})
     * @param Request $request
     * @param null $languageCode
     * @param PaginatorInterface $paginator
     * @param DataFinder $dataFinder
     * @return Response
     */
    public function list(Request $request, PaginatorInterface $paginator, DataFinder $dataFinder, $languageCode = null)
    {
        $language = $languageCode ? $dataFinder->retrieveLanguageOr404($languageCode) : null;
        $languageRepo = $this->getDoctrine()->getRepository(Language::class);
        $usedLanguages = $this->isGranted('ROLE_ADMIN')
            ? $languageRepo->findUsedInProject()
            : $languageRepo->findUsedInEnabledProject();

        $projectRepo = $this->getDoctrine()->getRepository(Project::class);
        $projectsQuery = $this->isGranted('ROLE_ADMIN')
            ? $projectRepo->findQueryForLanguage($language)
            : $projectRepo->findQueryEnabledForLanguage($language);
        $pagination = $paginator->paginate(
            $projectsQuery,
            $request->query->getInt('page', 1),
            24
        );

        return $this->render(
            'project/list.html.twig',
            [
                'projects' => $pagination,
                'language' => $language,
                'languagesWithProjects' => $usedLanguages,
            ]
        );
    }
}
