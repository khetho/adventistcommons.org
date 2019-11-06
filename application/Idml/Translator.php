<?php

namespace AdventistCommons\Idml;

/**
 * Translator for Idml files, to change content of package according to project
 * @package AdventistCommons\Export\Idml
 * @author    Vincent Beauvivre <vincent@beauvivre.fr>
 * @copyright 2019
 */
class Translator
{
    private $productModel;
    private $projectModel;
    
    public function __construct(
        \Product_model $productModel,
        \Project_model $projectModel
    ) {
        $this->productModel = $productModel;
        $this->projectModel = $projectModel;
    }
    
    public function translate(Holder $baseHolder, array $project): Holder
    {
        $holder = clone($baseHolder);
        $holder->setProject($project);
        $package = $holder->getPackage();
        $sections = $this->projectModel->getSections($project['id']);
        foreach ($sections as $section) {
            $story = $holder->getStory($section['story_key']);
            $contents = $this->productModel->getSectionContent($project['id'], $section['id']);
            foreach ($contents as $content) {
                $story->setContent($section['name'], $content['content_key'], $content['latest_revision']);
            }
            $package->addStory($story->getDomDocument());
        }
        $package->saveAll();
        
        return $holder;
    }
}
