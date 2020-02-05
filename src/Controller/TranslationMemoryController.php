<?php

namespace App\Controller;

use App\TranslationMemory\Translator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TranslationMemoryController extends AbstractController
{
    /**
     * @Route("/", name="app_translation_memory_index")
     * @return Response
     */
    public function index(): Response
    {
        return new Response('What ?', 200);
    }

    /**
     * @Route("/{sourceLanguageCode}/{translationLanguageCode}/{source}", name="app_translation_memory_from_string")
     * @param string $source
     * @param string $sourceLangCode
     * @param string $translationLangCode
     * @param Translator $translator
     * @return JsonResponse
     */
    public function translate(string $source, string $sourceLangCode, string $translationLangCode, Translator $translator)
    {
        return new JsonResponse($translator->translate($source, $translationLangCode, $sourceLangCode));
    }
}
