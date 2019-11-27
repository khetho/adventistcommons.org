<?php

namespace AdventistCommons\Idml;

use AdventistCommons\Idml\Entity\Holder;

/**
 * Translator for Idml files, to change content of package according to project
 * @package AdventistCommons\Export\Idml
 * @author    Vincent Beauvivre <vincent@beauvivre.fr>
 * @copyright 2019
 */
class Translator
{
    const COPY_KEY = '.ac_idml_ccopy.';

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
        $copyName = self::duplicateIdml($baseHolder->getZipFileName());
        $holder = new Holder($copyName, $baseHolder->getProduct());
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

    private static function duplicateIdml($previousName)
    {
        $removedSuffix = '.idml';
        if (substr($previousName, -strlen($removedSuffix)) === $removedSuffix) {
            $clearedPreviousName = substr($previousName, 0, -strlen($removedSuffix));
        }

        $copyKeyPosition = strpos(self::COPY_KEY, $clearedPreviousName);
        if ($copyKeyPosition !== false) {
            $clearedPreviousName = substr($clearedPreviousName, 0, $copyKeyPosition);
        }
        $copyName = $clearedPreviousName.self::COPY_KEY.self::uniqidReal().'.idml';
        copy($previousName, $copyName);

        return $copyName;
    }

    private static function uniqidReal($length = 13)
    {
        if (function_exists("random_bytes")) {
            $bytes = random_bytes(ceil($length / 2));
        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $bytes = openssl_random_pseudo_bytes(ceil($length / 2));
        } else {
            throw new \Exception("no cryptographically secure random function available");
        }

        return substr(bin2hex($bytes), 0, $length);
    }
}
