<?php

namespace App\Controller;

use App\Product\DownloadLogger;
use App\Product\Uploader\AttachmentUploader;
use App\Product\Form\Type\AddAttachmentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
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
     * @param AttachmentUploader $attachmentUploader
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
            $this->addFlash('success', 'messages.attachement.added');

            return $this->redirectToRoute('app_product_show', ['slug' => $product->getSlug()]);
        }

        $this->createNotFoundException();
    }

    /**
     * @Route("/{slug}/{languageCode}/{id}/download", name="app_attachment_download")
     * @param string $slug
     * @param $languageCode
     * @param $id
     * @param AttachmentUploader $attachmentUploader
     * @param DataFinder $dataFinder
     * @param DownloadLogger $downloadLogger
     * @return BinaryFileResponse
     */
    public function downloadAttachment(
        string $slug,
        $languageCode,
        $id,
        AttachmentUploader $attachmentUploader,
        DataFinder $dataFinder,
        DownloadLogger $downloadLogger
    ) {
        $attachment = $dataFinder->retrieveAttachmentOr404($slug, $languageCode, $id);
        $downloadLogger->log($attachment);
        $response = new BinaryFileResponse($attachmentUploader->getTargetPath().'/'.$attachment->getFileName());
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $attachment->getFileName());
        
        return $response;
    }
}
