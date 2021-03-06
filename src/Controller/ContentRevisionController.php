<?php

namespace App\Controller;

use App\ContentRevision\Lister;
use App\Project\Translation\TranslationAdder;
use App\Project\Translation\TranslationProofreader;
use App\Project\Translation\TranslationReviewer;
use App\Response\JsonResponseBuilder;
use App\Security\Voter\ProjectVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContentRevisionController extends AbstractController
{
    /**
     * @Route("/{slug}/{languageCode}/{sentenceId}", name="app_content_revision_put", methods={"PUT"})
     * @param $slug
     * @param $languageCode
     * @param $sentenceId
     * @param Request $request
     * @param DataFinder $dataFinder
     * @param TranslationAdder $translationAdder
     * @param JsonResponseBuilder $responseBuilder
     * @return Response
     */
    public function put(
        $slug,
        $languageCode,
        $sentenceId,
        Request $request,
        DataFinder $dataFinder,
        TranslationAdder $translationAdder,
        JsonResponseBuilder $responseBuilder
    ): Response {
        $project = $dataFinder->retrieveProjectOr404($slug, $languageCode);
        $sentence = $dataFinder->retrieveSentenceOr404($sentenceId, $project);
        $data = json_decode($request->getContent(), true);
        $contentRevision = $translationAdder->addTranslation($sentence, $project, $data['content']);

        return $responseBuilder->buildWithData(
            $contentRevision ? 'created' : 'no-action',
            [],
            201
        );
    }

    /**
     * @Route("/{slug}/{languageCode}/{sentenceId}/history", name="app_content_revision_history", methods={"GET"})
     * @param $slug
     * @param $languageCode
     * @param $sentenceId
     * @param DataFinder $dataFinder
     * @param JsonResponseBuilder $responseBuilder
     * @param Lister $lister
     * @return Response
     */
    public function history(
        $slug,
        $languageCode,
        $sentenceId,
        DataFinder $dataFinder,
        JsonResponseBuilder $responseBuilder,
        Lister $lister
    ): Response {
        $project = $dataFinder->retrieveProjectOr404($slug, $languageCode);
        $sentence = $dataFinder->retrieveSentenceOr404($sentenceId, $project);
        $revisions = $dataFinder->retrieveRevisions($sentence);
        $revisions = $lister->diff($revisions);

        return $responseBuilder->buildWithTemplate(
            'list',
            'section/_history.html.twig',
            [
                'revisions' => $revisions
            ]
        );
    }

    /**
     * @Route("/{slug}/{languageCode}/{sentenceId}/proofread", name="app_content_revision_proofread", methods={"POST"})
     * @param $slug
     * @param $languageCode
     * @param $sentenceId
     * @param DataFinder $dataFinder
     * @param JsonResponseBuilder $responseBuilder
     * @param TranslationProofreader $proofreader
     * @return Response
     */
    public function proofread(
        $slug,
        $languageCode,
        $sentenceId,
        DataFinder $dataFinder,
        JsonResponseBuilder $responseBuilder,
        TranslationProofreader $proofreader
    ) {
        $contentRevision = $dataFinder->retrieveLatestContentRevisionOr404($slug, $languageCode, $sentenceId);
        if (!$proofreader->proofreadTranslation($contentRevision)) {
            return $responseBuilder->buildErrorResponse('You are not allowed');
        }

        return $responseBuilder->buildWithData('updated');
    }

    /**
     * @Route("/{slug}/{languageCode}/{sentenceId}/review", name="app_content_revision_review", methods={"POST"})
     * @param $slug
     * @param $languageCode
     * @param $sentenceId
     * @param DataFinder $dataFinder
     * @param JsonResponseBuilder $responseBuilder
     * @param TranslationReviewer $reviewer
     * @return Response
     */
    public function review(
        $slug,
        $languageCode,
        $sentenceId,
        DataFinder $dataFinder,
        JsonResponseBuilder $responseBuilder,
        TranslationReviewer $reviewer
    ) {
        $contentRevision = $dataFinder->retrieveLatestContentRevisionOr404($slug, $languageCode, $sentenceId);
        if (!$reviewer->reviewTranslation($contentRevision)) {
            return $responseBuilder->buildErrorResponse('You are not allowed');
        }

        return $responseBuilder->buildWithData('updated');
    }
}
