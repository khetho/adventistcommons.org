<?php

namespace App\Controller;

use App\Project\Translation\TranslationAdder;
use App\Response\JsonResponseBuilder;
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
            $contentRevision ? 201 : 204
        );
    }

    /**
     * @Route("/{slug}/{languageCode}/{sentenceId}", name="app_content_revision_history")
     * @param $slug
     * @param $languageCode
     * @param $sentenceId
     * @param DataFinder $dataFinder
     * @param JsonResponseBuilder $responseBuilder
     * @return Response
     */
    public function history(
        $slug,
        $languageCode,
        $sentenceId,
        DataFinder $dataFinder,
        JsonResponseBuilder $responseBuilder
    ): Response {
        $project = $dataFinder->retrieveProjectOr404($slug, $languageCode);
        $sentence = $dataFinder->retrieveSentenceOr404($sentenceId, $project);
        $revisions = $dataFinder->retrieveRevisions($sentence);

        return $responseBuilder->buildWithTemplate(
            'list',
            'section/_history.html.twig',
            [
                'revisions' => $revisions
            ]
        );
    }
}
