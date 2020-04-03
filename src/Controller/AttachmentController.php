<?php

namespace App\Controller;

use App\Entity\Attachment;
use App\Entity\Product;
use App\Entity\Project;
use App\Product\DownloadLogger;
use App\Product\Uploader\AttachmentUploader;
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

class AttachmentController extends AbstractController
{
    private $attachmentUploader;
    private $downloadLogger;

    public function __construct(AttachmentUploader $attachmentUploader, DownloadLogger $downloadLogger)
    {
        $this->attachmentUploader = $attachmentUploader;
        $this->downloadLogger = $downloadLogger;
    }

    /**
     * @Route("/{slug}/add-attachment", name="app_product_attachment_add")
     * @param Request $request
     * @param string $slug
     * @param DataFinder $dataFinder
     * @return Response
     * @throws Exception
     */
    public function addProductAttachment(Request $request, string $slug, DataFinder $dataFinder)
    {
        $product = $dataFinder->retrieveProductOr404($slug);
        return $this->addAttachment($request, $product);
    }

    /**
     * @Route("/{slug}/{languageCode}/add-attachment", name="app_project_attachment_add")
     * @param Request $request
     * @param string $slug
     * @param string $languageCode
     * @param DataFinder $dataFinder
     * @return Response
     * @throws Exception
     */
    public function addProjectAttachment(Request $request, string $slug, string $languageCode, DataFinder $dataFinder)
    {
        $project = $dataFinder->retrieveProjectOr404($slug, $languageCode);
        return $this->addAttachment($request, $project);
    }

    private function addAttachment(Request $request, $target)
    {
        if (!($target instanceof Project || $target instanceof Product)) {
            throw new \Exception('Cannot set attachement to something else than a product or a project');
        }

        if ($target instanceof Product) {
            $project = null;
            $product = $target;
        } elseif ($target instanceof Project) {
            $project = $target;
            $product = $project->getProduct();
        }

        $addAttachmentForm = $this->createForm(AddAttachmentType::class, null);
        $addAttachmentForm->handleRequest($request);
        if ($addAttachmentForm->isSubmitted() && $addAttachmentForm->isValid()) {
            $attachment = $addAttachmentForm->getData();
            $project ? $attachment->setProject($project) : $attachment->setProduct($product);
            $attachment = $this->attachmentUploader->upload($attachment);
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($attachment);
            $manager->flush();
            $this->addFlash('success', 'messages.attachment.added');

            return $this->redirectToRoute('app_product_show', ['slug' => $product->getSlug()]);
        }

        return $this->render(
            'attachment/add.html.twig',
            [
                'product' => $product,
                'addForm' => $addAttachmentForm->createView(),
            ]
        );
    }

    /**
     * @Route("/{slug}/{id}/download", name="app_product_attachment_download")
     * @param string $slug
     * @param $id
     * @param DataFinder $dataFinder
     * @return Response
     * @throws Exception
     */
    public function downloadProductAttachment(
        string $slug,
        $id,
        DataFinder $dataFinder
    ) {
        $attachment = $dataFinder->retrieveAttachmentOr404($slug, null, $id);

        return $this->download($attachment);
    }

    /**
     * @Route("/{slug}/{languageCode}/{id}/download", name="app_project_attachment_download")
     * @param string $slug
     * @param $languageCode
     * @param $id
     * @param DataFinder $dataFinder
     * @return Response
     * @throws Exception
     */
    public function downloadProjectAttachment(
        string $slug,
        $languageCode,
        $id,
        DataFinder $dataFinder
    ) {
        $attachment = $dataFinder->retrieveAttachmentOr404($slug, $languageCode, $id);

        return $this->download($attachment);
    }

    /**
     * @param Attachment $attachment
     * @return Response
     * @throws Exception
     */
    private function download(Attachment $attachment): Response
    {
        $this->downloadLogger->log($attachment);
        $response = new BinaryFileResponse($this->attachmentUploader->getTargetPath().'/'.$attachment->getFileName());
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $attachment->getFileName());

        return $response;
    }
}
