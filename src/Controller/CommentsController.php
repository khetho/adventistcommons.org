<?php

namespace App\Controller;

use App\Response\JsonResponseBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentsController extends AbstractController
{
    /**
     * @Route("/{slug}/{languageCode}/{sentenceId}", name="app_comment_for_sentence")
     * @param $slug
     * @param $languageCode
     * @param $sentenceId
     * @param DataFinder $dataFinder
     * @param JsonResponseBuilder $responseBuilder
     * @return Response
     */
    public function getForSentence(
        $slug,
        $languageCode,
        $sentenceId,
        DataFinder $dataFinder,
        JsonResponseBuilder $responseBuilder
    ) {
        $project = $dataFinder->retrieveProjectOr404($slug, $languageCode);
        $sentence = $dataFinder->retrieveSentenceOr404($sentenceId, $project);

        return $responseBuilder->buildWithTemplate(
            'list',
            'section/_comments.html.twig',
            [
                'comments' => [],
                'sentence' => $sentence->getContent(),
            ]
        );
    }
}
