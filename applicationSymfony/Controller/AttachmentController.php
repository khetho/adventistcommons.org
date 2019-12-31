<?php

namespace App\Controller;

use App\Entity\Product;
use App\Product\CoverUploader;
use App\Product\AttachmentUploader;
use App\Product\IdmlUploader;
use App\Product\Form\Type\IdmlType;
use App\Product\Form\Type\DeleteType;
use App\Product\Form\Type\GeneralType;
use App\Product\Form\Type\SpecsType;
use App\Project\Form\Type\AddType;
use App\Product\Form\Type\AddAttachmentType;
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

class AttachmentController extends AbstractController
{
    /**
     * @Route("/{slug}/add-attachment", name="app_attachment_add")
     * @param Request $request
     * @param string $slug
     * @param DataFinder $dataFinder
     * @return Response
     */
    public function addAttachment(Request $request, string $slug, DataFinder $dataFinder, AttachmentUploader $attachmentUploader)
    {
        $product = $dataFinder->retrieveProductOr404($slug);
        $addAttachmentForm = $this->createForm(AddAttachmentType::class, null);
        $addAttachmentForm->handleRequest($request);
        if ($addAttachmentForm->isSubmitted() && $addAttachmentForm->isValid()) {
            $attachment = $addAttachmentForm->getData();
            $attachment = $attachmentUploader->upload($attachment, $product);
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($attachment);
            $manager->flush();
            $this->addFlash('success', 'Attachment successfully added');

            return $this->redirectToRoute('app_product_show', ['slug' => $product->getSlug()]);
        }

        $this->createNotFoundException();
    }
        
    /**
     * @Route("/{slug}/{languageCode}/{id}/download", name="app_attachment_download")
     * @param string $slug
     * @param $languageCode
     * @param $id
     * @param AttachmentlUploader $attachmentlUploader
     * @param DataFinder $dataFinder
     * @return BinaryFileResponse
     */
    public function downloadAttachment(string $slug, $languageCode, $id, AttachmentUploader $attachmentlUploader, DataFinder $dataFinder)
    {
        $attachment = $dataFinder->retrieveAttachmentOr404($slug, $languageCode, $id);
        $response = new BinaryFileResponse($attachmentlUploader->getTargetPath().'/'.$attachment->getFilename());
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $attachment->getFilename());
        
        return $response;
    }
}
