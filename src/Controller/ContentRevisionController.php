<?php

namespace App\Controller;

use App\Entity\ContentRevision;
use App\Project\Translation\TranslationAdder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

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
        $data = json_decode($request->getContent(), true);
        $contentRevision = $translationAdder->addTranslation($sentence, $project, $data['content']);

        if ($contentRevision) {
            return new JsonResponse(
                [
                    'status' => 'success',
                    'result' => 'created',
                ],
                201
            );
        }

        return new JsonResponse(
            [
                'status' => 'success',
                'result' => 'no-action',
            ],
            204
        );
    }

    /**
     * @Route("/{slug}/{languageCode}/{sentenceId}", name="app_content_revision_history")
     * @param $slug
     * @param $languageCode
     * @param $sentenceId
     * @param DataFinder $dataFinder
     * @param EntityManagerInterface
     * @param Environement
     * @return Response
     */
    public function history(
        $slug,
        $languageCode,
        $sentenceId,
        DataFinder $dataFinder,
        EntityManagerInterface $manager,
        Environment $twig
    ) {
        $project = $dataFinder->retrieveProjectOr404($slug, $languageCode);
        $sentence = $dataFinder->retrieveSentenceOr404($sentenceId, $project);
        $revisions = $manager->getRepository(ContentRevision::class)->findBy([
            'sentence' => $sentence
        ]);

        return new JsonResponse(
            [
                'status' => 'success',
                'result' => 'list',
                'html'   => $twig->render(
                    'section/_history.html.twig',
                    [
                        'revisions' => $revisions
                    ]
                ),
                'target' => '.js-revisions',
            ],
            200
        );
    }
}
