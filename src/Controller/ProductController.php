<?php

namespace App\Controller;

use App\Entity\ContentRevision;
use App\Form\UploaderAggregator;
use App\Product\DownloadLogger;
use App\Product\Form\Type\DeleteConfirmType;
use App\Product\Uploader\IdmlUploader;
use App\Product\Uploader\PdfOffsetUploader;
use App\Product\Uploader\PdfDigitalUploader;
use App\Product\Form\Type\IdmlType;
use App\Product\Form\Type\PdfsType;
use App\Product\Form\Type\DeleteType;
use App\Product\Form\Type\GeneralType;
use App\Product\Form\Type\SpecsType;
use App\Project\Form\Type\AddType;
use App\Product\Form\Type\AddAttachmentType;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/{slug}", name="app_product_show")
     * @param string $slug
     * @param DataFinder $dataFinder
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
        $addAttachmentForm = $this->createForm(
            AddAttachmentType::class,
            null,
            [
                'action' => $this->generateUrl(
                    'app_attachment_add',
                    ['slug' => $product->getSlug()]
                ),
            ]
        );

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'projectAddForm' => $projectAddForm->createView(),
            'addAttachmentForm' => $addAttachmentForm->createView(),
        ]);
    }

    /**
     * @Route("/{slug}/product.idml", name="app_product_download_idml")
     * @param string $slug
     * @param IdmlUploader $idmlUploader
     * @param DataFinder $dataFinder
     * @param DownloadLogger $downloadLogger
     * @return BinaryFileResponse
     * @throws Exception
     */
    public function downloadIdml(
        string $slug,
        IdmlUploader $idmlUploader,
        DataFinder $dataFinder,
        DownloadLogger $downloadLogger
    ) {
        $product = $dataFinder->retrieveProductOr404($slug);
        if (!$product->getIdmlFilename()) {
            $this->createNotFoundException();
        }
        $downloadLogger->log($product);

        $response = new BinaryFileResponse($idmlUploader->getTargetPath().'/'.$product->getIdmlFilename());
        $response->headers->set('Content-Type', 'application/zip');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $product->getIdmlFilename());
        
        return $response;
    }

    /**
     * @Route("/{slug}/offset.pdf", name="app_product_download_offset_pdf")
     * @param string $slug
     * @param PdfOffsetUploader $offsetUploader
     * @param DataFinder $dataFinder
     * @param DownloadLogger $downloadLogger
     * @return BinaryFileResponse
     * @throws Exception
     */
    public function downloadOffsetPdf(
        string $slug,
        PdfOffsetUploader $offsetUploader,
        DataFinder $dataFinder,
        DownloadLogger $downloadLogger
    ) {
        $product = $dataFinder->retrieveProductOr404($slug);
        if (!$product->getIdmlFilename()) {
            $this->createNotFoundException();
        }
        $downloadLogger->log($product);

        $response = new BinaryFileResponse($offsetUploader->getTargetPath().'/'.$product->getPdfOffsetFilename());
        $response->headers->set('Content-Type', 'application/pdf');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $product->getPdfOffsetFilename());
        
        return $response;
    }

    /**
     * @Route("/{slug}/digital.pdf", name="app_product_download_digital_pdf")
     * @param string $slug
     * @param PdfDigitalUploader $digitalUploader
     * @param DataFinder $dataFinder
     * @param DownloadLogger $downloadLogger
     * @return BinaryFileResponse
     * @throws Exception
     */
    public function downloadDigitalPdf(
        string $slug,
        PdfDigitalUploader $digitalUploader,
        DataFinder $dataFinder,
        DownloadLogger $downloadLogger
    ) {
        $product = $dataFinder->retrieveProductOr404($slug);
        if (!$product->getIdmlFilename()) {
            $this->createNotFoundException();
        }
        $downloadLogger->log($product);

        $response = new BinaryFileResponse($digitalUploader->getTargetPath().'/'.$product->getPdfDigitalFilename());
        $response->headers->set('Content-Type', 'application/pdf');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $product->getPdfDigitalFilename());
        
        return $response;
    }

    /**
     * @Route("/{slug}/edit", name="app_product_edit")
     * @param string $slug
     * @param Request $request
     * @param UploaderAggregator $uploader
     * @param DataFinder $dataFinder
     * @return Response
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @throws Exception
     */
    public function edit($slug, Request $request, UploaderAggregator $uploader, DataFinder $dataFinder)
    {
        $product = $dataFinder->retrieveProductOr404($slug);
        $submittedProduct = null;

        $generalForm = $this->createForm(GeneralType::class, $product);
        $generalForm->handleRequest($request);
        if ($generalForm->isSubmitted() && $generalForm->isValid()) {
            $submittedProduct = $generalForm->getData();
            $submittedProduct = $uploader->upload($submittedProduct);
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
            $submittedProduct = $uploader->upload($submittedProduct);
        }

        $pdfsForm = $this->createForm(PdfsType::class, $product);
        $pdfsForm->handleRequest($request);
        if ($pdfsForm->isSubmitted() && $pdfsForm->isValid()) {
            $submittedProduct = $pdfsForm->getData();
            $submittedProduct = $uploader->upload($submittedProduct);
        }

        if ($submittedProduct) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($submittedProduct);
            $manager->flush();
            $this->addFlash('success', 'messages.product.saved');

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
            'pdfsForm'     => $pdfsForm->createView(),
            'deleteForm'   => $deleteForm->createView(),
        ]);
    }

    /**
     * @Route("/{slug}/delete", name="app_product_delete")
     * @param string $slug
     * @param Request $request
     * @param DataFinder $dataFinder
     * @return Response
     */
    public function delete(string $slug, Request $request, DataFinder $dataFinder)
    {
        $product = $dataFinder->retrieveProductOr404($slug);
        $deleteForm = $this->createForm(DeleteType::class, $product);
        $deleteForm->handleRequest($request);
        if ($deleteForm->isSubmitted() && $deleteForm->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $submittedProduct = $deleteForm->getData();
            $count = $manager->getRepository(ContentRevision::class)->countForProduct($product);
            if ($count) {
                return $this->redirectToRoute('app_product_delete_confirm', ['slug' => $slug]);
            }

            $manager->remove($submittedProduct);
            $manager->flush();
            $this->addFlash('success', 'messages.product.deleted');

            return $this->redirectToRoute('app_product_list');
        }

        $this->createNotFoundException();
    }


    /**
     * @Route("/{slug}/delete/confirm", name="app_product_delete_confirm")
     * @param string $slug
     * @param Request $request
     * @param DataFinder $dataFinder
     * @return Response
     */
    public function deleteConfirm(string $slug, Request $request, DataFinder $dataFinder)
    {
        $product = $dataFinder->retrieveProductOr404($slug);
        $confirmDeleteForm = $this->createForm(DeleteConfirmType::class, $product);
        $manager = $this->getDoctrine()->getManager();
        $confirmDeleteForm->handleRequest($request);
        if ($confirmDeleteForm->isSubmitted() && $confirmDeleteForm->isValid()) {
            $manager->remove($product);
            $manager->flush();
            $this->addFlash('success', 'messages.product.deleted');

            return $this->redirectToRoute('app_product_list');
        }
        $count = $manager->getRepository(ContentRevision::class)->countForProduct($product);

        return $this->render('product/delete.html.twig', [
            'product'          => $product,
            'deleteForm'       => $confirmDeleteForm->createView(),
            'translationCount' => $count,
        ]);
    }
}
