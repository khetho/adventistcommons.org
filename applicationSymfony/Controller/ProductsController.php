<?php

namespace App\Controller;

use App\Product\FilterApplier;
use App\Form\UploaderAggregator;
use App\Product\CurrentFilterManager;
use App\Product\Form\Type\AddType;
use App\Product\Form\Type\FilterType;
use App\Product\Form\Type\ValidateIdmlType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductsController extends AbstractController
{
    /**
     * @Route("/", name="app_product_list")
     * @param Request $request
     * @param FilterApplier $filterApplier
     * @param CurrentFilterManager $currentFilterManager
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
            ValidateIdmlType::class,
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
     * @param CurrentFilterManager $currentFilterManager
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
     * @param UploaderAggregator $uploaderAggregator
     * @return Response
     * @throws \Exception
     */
    public function add(Request $request, UploaderAggregator $uploaderAggregator)
    {
        $addForm = $this->createForm(AddType::class);
        $addForm->handleRequest($request);
        if ($addForm->isSubmitted() && $addForm->isValid()) {
            $product = $addForm->getData();
            $product = $uploaderAggregator->upload($product);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();
            $this->addFlash('success', 'messages.product.created');

            return $this->redirectToRoute('app_product_list');
        }

        return $this->render('product/add/add.html.twig', [
            'productAddForm' => $addForm->createView(),
        ]);
    }
}
