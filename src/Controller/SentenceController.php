<?php

namespace App\Controller;

use App\Response\JsonResponseBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SentenceController extends AbstractController
{
    /**
     * @Route("/{slug}/{languageCode}/{sentenceId}", name="app_sentence_info")
     * @param $slug
     * @param $languageCode
     * @param $sentenceId
     * @param DataFinder $dataFinder
     * @param JsonResponseBuilder $responseBuilder
     * @return Response
     */
    public function infoForSentence(
        $slug,
        $languageCode,
        $sentenceId,
        DataFinder $dataFinder,
        JsonResponseBuilder $responseBuilder
    ) {
        $info = $dataFinder->retrieveSentenceInfoOr404($slug, $languageCode, $sentenceId);
        return $responseBuilder->buildWithData(
            'info',
            [
                'revisions_count'     => $info['cr_count'],
                'comments_count'      => 0,
                'translation_machine' => 'This machine translation is a fake',
                'translation_memory'  => [
                    [
                        'score'   => 1,
                        'content' => 'This translation memory result is a fake'
                    ],
                    [
                        'score'   => .8,
                        'content' => 'This translation memory result is a fake too'
                    ]
                ],
            ]
        );
    }
}
