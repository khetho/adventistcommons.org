<?php

namespace App\Controller;

use App\Entity\Product;
use App\Product\CoverUploader;
use App\Product\IdmlUploader;
use App\Product\Form\Type\IdmlType;
use App\Product\Form\Type\DeleteType;
use App\Product\Form\Type\GeneralType;
use App\Product\Form\Type\SpecsType;
use App\Project\Form\Type\AddType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */

class ProductController extends AbstractController
{
    /**
     * @Route("/{slug}", name="app_product_show")
     * @param string $slug
     * @return Response
     */
    public function show(string $slug, DataFinder $dataFinder)
    {
        $product = $dataFinder->retrieveProductOr404($slug);
        $projectAddForm = $this->createForm(
            AddType::class,
            null,
            [
                'action' => $this->generateUrl(
                    'app_project_add',
                    ['slug' => $product->getSlug()]
                ),
            ]
        );

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'projectAddForm' => $projectAddForm->createView(),
        ]);
    }

    /**
     * @Route("/{slug}/product.idml", name="app_product_download_idml")
     * @param string $slug
     * @param IdmlUploader $idmlUploader
     * @return BinaryFileResponse
     */
    public function downloadIdml(string $slug, IdmlUploader $idmlUploader, DataFinder $dataFinder)
    {
        $product = $dataFinder->retrieveProductOr404($slug);
        if (!$product->getIdmlFilename()) {
            $this->createNotFoundException();
        }
        
        $response = new BinaryFileResponse($idmlUploader->getTargetPath().'/'.$product->getIdmlFilename());
        $response->headers->set('Content-Type', 'application/zip');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $product->getIdmlFilename());
        
        return $response;
    }

    /**
     * @Route("/{slug}/edit", name="app_product_edit")
     * @param string $slug
     * @param Request $request
     * @param CoverUploader $coverUploader
     * @param IdmlUploader $idmlUploader
     * @return Response
     * @throws \Exception
     */
    public function edit($slug, Request $request, CoverUploader $coverUploader, IdmlUploader $idmlUploader, DataFinder $dataFinder)
    {
        $product = $dataFinder->retrieveProductOr404($slug);
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
            $submittedProduct = $idmlUploader->upload($submittedProduct);
        }

        if ($submittedProduct) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($submittedProduct);
            $manager->flush();
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
     * @Route("/{slug}/delete", name="app_product_delete")
     * @param string $slug
     * @param Request $request
     * @return Response
     */
    public function delete(string $slug, Request $request, DataFinder $dataFinder)
    {
        $product = $dataFinder->retrieveProductOr404($slug);
        $deleteForm = $this->createForm(DeleteType::class, $product);
        $deleteForm->handleRequest($request);
        if ($deleteForm->isSubmitted() && $deleteForm->isValid()) {
            $submittedProduct = $deleteForm->getData();
            $manager = $this->getDoctrine()->getManager();
            $manager->remove($submittedProduct);
            $manager->flush();
            $this->addFlash('success', 'Product successfully deleted');

            return $this->redirectToRoute('app_product_list');
        }

        $this->createNotFoundException();
    }

    /**
     * @Route("/{slug}/add-attachment", name="app_product_add_attachemnt")
     * @param Request $request
     * @param string $slug
     * @param DataFinder $dataFinder
     * @return Response
     */
    public function addAttachment(Request $request, string $slug, DataFinder $dataFinder)
    {
        $product = $dataFinder->retrieveProductOr404($slug);
        $addAttachmentType = $this->createForm(AddAttachmentType::class, null, ['product' => $product]);
        $addAttachmentType->handleRequest($request);
        if ($addAttachmentType->isSubmitted() && $addAttachmentType->isValid()) {
            $attachment = $addAttachmentType->getData();
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($attachment);
            $manager->flush();
            $this->addFlash('success', 'Attachment successfully added');

            return $this->redirectToRoute('app_product_list');
        }

        $this->createNotFoundException();
    }
}
