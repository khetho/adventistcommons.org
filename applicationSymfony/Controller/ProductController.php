<?php

namespace App\Controller;

use App\Entity\Product;
use App\Product\FilterApplier;
use App\Product\CoverUploader;
use App\Product\CurrentFilterManager;
use App\Product\Form\Type\AddType;
use App\Product\Form\Type\IdmlType;
use App\Product\Form\Type\DeleteType;
use App\Product\Form\Type\GeneralType;
use App\Product\Form\Type\FilterType;
use App\Product\Form\Type\SpecsType;
use App\Product\Form\Type\ValidateImdlType;
use Hybridauth\Exception\NotImplementedException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/", name="app_product_list")
     * @param Request $request
     * @param FilterApplier $filterApplier
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function list(
        Request $request,
        FilterApplier $filterApplier,
        CurrentFilterManager $currentFilterManager,
        PaginatorInterface $paginator
    ) {
        $currentFilter = $currentFilterManager->getCurrentFilterStatus();
        $filterForm = $this->createForm(FilterType::class, $currentFilter);
        $filterForm->handleRequest($request);
        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $currentFilterManager->setCurrentFilterStatus($filterForm->getData());

            return $this->redirectToRoute('app_product_list');
        }
        $query = $filterApplier->getProducts();

        $idmlValidationForm = $this->createForm(
            ValidateImdlType::class,
            null,
            [
                'action' => $this->generateUrl('app_product_validate_idml'),
            ]
        );
        $addForm = $this->createForm(
            AddType::class,
            null,
            [
                'action' => $this->generateUrl('app_product_add'),
            ]
        );

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            24
        );

        return $this->render('product/list.html.twig', [
            'products' => $pagination,
            'filterForm' => $filterForm->createView(),
            'productAddForm' => $addForm->createView(),
            'idmlValidationForm' => $idmlValidationForm->createView(),
        ]);
    }

    /**
     * @Route("/reset-filters", name="app_product_reset_filters")
     * @param FilterApplier $filterApplier
     * @return Response
     */
    public function resetFilters(CurrentFilterManager $currentFilterManager)
    {
        $currentFilterManager->reset();
        
        return $this->redirectToRoute('app_product_list');
    }

    /**
     * @Route("/add", name="app_product_add")
     * @param Request $request
     * @return Response
     */
    public function add(Request $request, CoverUploader $coverUploader)
    {
        $addForm = $this->createForm(AddType::class);
        $addForm->handleRequest($request);
        if ($addForm->isSubmitted() && $addForm->isValid()) {
            $product = $addForm->getData();
            $product = $coverUploader->upload($product);
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();
            $this->addFlash('success', 'Product created successfully.');

            return $this->redirectToRoute('app_product_list');
        }

        return $this->render('product/add/add.html.twig', [
            'productAddForm' => $addForm->createView(),
        ]);
    }

    /**
     * @Route("/validate-idml", name="app_product_validate_idml")
     * @param Request $request
     * @return Response
     */
    public function validateIdml(Request $request)
    {
        $idmlValidationForm = $this->createForm(ValidateImdlType::class);
        $idmlValidationForm->handleRequest($request);
        if ($idmlValidationForm->isSubmitted() && $idmlValidationForm->isValid()) {
            $this->addFlash('success', 'Idml validated successfully.');

            return $this->redirectToRoute('app_product_validate_idml');
        }

        return $this->render('product/idmlValidation/validate_idml.html.twig', [
            'idmlValidationForm' => $idmlValidationForm->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="app_product_show")
     * @param string $slug
     * @return Response
     */
    public function show(string $slug)
    {
        $product = $this->retrieveProductOr404($slug);

        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }
    /**
     * @Route("/{slug}", name="app_product_download_idml")
     * @param string $slug
     * @throws NotImplementedException
     */
    public function downloadIdml(string $slug)
    {
        $product = $this->retrieveProductOr404($slug);
        throw new NotImplementedException();
    }

    /**
     * @Route("/{slug}/edit", name="app_product_edit")
     * @param string $slug
     * @param Request $request
     * @return Response
     */
    public function edit($slug, Request $request, CoverUploader $coverUploader)
    {
        $product = $this->retrieveProductOr404($slug);
        $submittedProduct = null;

        $generalForm = $this->createForm(GeneralType::class, $product);
        $generalForm->handleRequest($request);
        if ($generalForm->isSubmitted() && $generalForm->isValid()) {
            $submittedProduct = $generalForm->getData();
            $submittedProduct = $coverUploader->upload($submittedProduct);
        }

        $specsForm = $this->createForm(SpecsType::class, $product);
        $specsForm->handleRequest($request);
        if ($specsForm->isSubmitted() && $specsForm->isValid()) {
            $submittedProduct = $specsForm->getData();
        }

        $idmlForm = $this->createForm(IdmlType::class, $product);
        $idmlForm->handleRequest($request);
        if ($idmlForm->isSubmitted() && $idmlForm->isValid()) {
            $submittedProduct = $idmlForm->getData();
        }

        if ($submittedProduct) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($submittedProduct);
            $em->flush();
            $this->addFlash('success', 'Product successfully saved');

            return $this->redirectToRoute('app_product_edit', ['slug' => $product->getSlug()]);
        }
        $deleteForm = $this->createForm(
            DeleteType::class,
            $product,
            [
                'action' => $this->generateUrl('app_product_delete', ['slug' => $product->getSlug()]),
            ]
        );

        return $this->render('product/edit.html.twig', [
            'product'      => $product,
            'generalForm'  => $generalForm->createView(),
            'specsForm'    => $specsForm->createView(),
            'idmlForm'     => $idmlForm->createView(),
            'deleteForm'   => $deleteForm->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="app_product_delete")
     * @param string $slug
     * @param Request $request
     * @return Response
     */
    public function delete(string $slug, Request $request)
    {
        $product = $this->retrieveProductOr404($slug);
        $deleteForm = $this->createForm(DeleteType::class, $product);
        $deleteForm->handleRequest($request);
        if ($deleteForm->isSubmitted() && $deleteForm->isValid()) {
            $submittedProduct = $deleteForm->getData();
            $em = $this->getDoctrine()->getManager();
            $em->remove($submittedProduct);
            $em->flush();
            $this->addFlash('success', 'Product successfully deleted');

            return $this->redirectToRoute('app_product_list');
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    private function retrieveProductOr404($slug): Product
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->findOneBy([
            'slug' => $slug,
        ]);
        if (!$product) {
            throw new NotFoundHttpException();
        }

        return $product;
    }
}
