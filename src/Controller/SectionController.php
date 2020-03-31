<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SectionController extends AbstractController
{
    /**
     * @Route("/{slug}/{languageCode}/{sectionName}", name="app_section_edit")
     * @param $slug
     * @param $languageCode
     * @param $sectionName
     * @param DataFinder $dataFinder
     * @return Response
     */
    public function edit($slug, $languageCode, $sectionName, DataFinder $dataFinder)
    {
        $project = $dataFinder->retrieveProjectOr404($slug, $languageCode);
        $section = $dataFinder->retrieveSectionOr404($sectionName, $project);
        $section = $dataFinder->addLatestTranslations($section, $project);

        return $this->render(
            'section/edit.html.twig',
            [
                'section' => $section,
                'project' => $project,
                'product' => $project->getProduct(),
            ]
        );
    }
}
