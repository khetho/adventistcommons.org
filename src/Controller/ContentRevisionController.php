<?php

namespace App\Controller;

use App\Project\Translation\TranslationAdder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @return Response
     */
    public function put(
        $slug,
        $languageCode,
        $sentenceId,
        Request $request,
        DataFinder $dataFinder,
        TranslationAdder $translationAdder
    ) {
        $project = $dataFinder->retrieveProjectOr404($slug, $languageCode);
        $sentence = $dataFinder->retrieveSentenceOr404($sentenceId, $project);
        $translationAdder->addTranslation($sentence, $project, $request->request->get('value'));

        return new JsonResponse(['status' => 'success']);
    }
}
