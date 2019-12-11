<?php

namespace App\Controller;

use App\Entity\Product;
use App\Product\FilterApplier;
use App\Product\Form\Type\AddType;
use App\Product\Form\Type\FilterType;
use App\Product\Form\Type\ValidateImdlType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Knp\DictionaryBundle\Dictionary\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/", name="app_product_list")
     * @param Request $request
     * @param FilterApplier $filterApplier
     * @param EntityManagerInterface $em
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function list(
        Request $request,
        FilterApplier $filterApplier,
        EntityManagerInterface $em,
        PaginatorInterface $paginator
    ) {
        $filterForm = $this->createForm(FilterType::class);
        $filterForm->handleRequest($request);
        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $query = $filterApplier->getProducts($filterForm->getData());
        } else {
            $dql   = sprintf('SELECT a FROM %s a', Product::class);
            $query = $em->createQuery($dql);
        }

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
     * @Route("/add", name="app_product_add")
     * @param Request $request
     * @return Response
     */
    public function add(Request $request, Collection $dictionaries)
    {
        $addForm = $this->createForm(AddType::class);
        $addForm->handleRequest($request);
        if ($addForm->isSubmitted() && $addForm->isValid()) {
            $product = $addForm->getData();
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
}
