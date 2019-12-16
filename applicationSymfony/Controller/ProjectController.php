<?php

namespace App\Controller;

use App\Entity\Language;
use App\Entity\Product;
use App\Entity\Project;
use App\Project\Form\Type\AddType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    /**
     * @Route("/{slug}/create-project", name="app_project_add")
     * @param Request $request
     * @return Response
     */
    public function add(Request $request, string $slug)
    {
        $this->retrieveProductOr404($slug);
        $product = $this->getDoctrine()->getRepository(Product::class)->findOneBy([
            'slug' => $slug,
        ]);
        if (!$product) {
            $this->createNotFoundException();
        }

        $addForm = $this->createForm(AddType::class);
        $addForm->handleRequest($request);
        if ($addForm->isSubmitted() && $addForm->isValid()) {
            $project = $addForm->getData();
            $project->setProduct($product);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($project);
            $entityManager->flush();
            $this->addFlash('success', 'Project created successfully.');

            return $this->redirectToRoute('app_project_show', [
                'slug' => $product->getSlug(),
                'languageCode' => $project->getLanguage()->getCode()
            ]);
        }

        return $this->render('product/add/add.html.twig', [
            'productAddForm' => $addForm->createView(),
        ]);
    }

    /**
     * @Route("/{slug}/{languageCode}", name="app_project_show")
     * @param Request $request
     * @return Response
     */
    public function show($slug, $languageCode)
    {
        $product = $this->retrieveProductOr404($slug);
        $language = $this->retrievelanguageOr404($languageCode);
        $project = $this->getDoctrine()->getRepository(Project::class)->findOneBy([
            'language' => $language,
            'product' => $product,
        ]);
        if (!$project) {
            throw new NotFoundHttpException();
        }

        return $this->render('project/show.html.twig', [
            'project' => $project,
        ]);
    }

    private function retrieveProductOr404(string $slug): Product
    {
        /** @var Product $product */
        $product = $this->getDoctrine()->getRepository(Product::class)->findOneBy([
            'slug' => $slug,
        ]);
        if (!$product) {
            throw new NotFoundHttpException();
        }

        return $product;
    }

    private function retrievelanguageOr404(string $code): Language
    {
        /** @var Language $language */
        $language = $this->getDoctrine()->getRepository(Language::class)->findOneBy([
            'code' => $code,
        ]);
        if (!$language) {
            throw new NotFoundHttpException();
        }

        return $language;
    }
}
