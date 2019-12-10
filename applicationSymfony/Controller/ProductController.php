<?php

namespace App\Controller;

use App\Entity\Product;
use App\Product\FilterApplier;
use App\Product\Form\Type\FilterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="app_product_list")
     * @param Request $request
     * @param FilterApplier $filterApplier
     * @return Response
     */
    public function list(Request $request, FilterApplier $filterApplier)
    {
        $repo = $this->getDoctrine()->getRepository(Product::class);

        $filterForm = $this->createForm(FilterType::class);
        $filterForm->handleRequest($request);
        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $products = $filterApplier->getProducts($filterForm->getData());
        } else {
            $products = $repo->findAll();
        }

        return $this->render('product/list.html.twig', [
            'products' => $products,
            'filterForm' => $filterForm->createView(),
        ]);
    }
}
