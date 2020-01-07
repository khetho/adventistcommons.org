<?php

namespace AdventistCommons\Idml;

use AdventistCommons\Idml\Entity\Holder;
use App\Entity\Project;
use \LogicException;

/**
 * Translator for Idml files, to change content of package according to project
 * @package AdventistCommons\Export\Idml
 * @author    Vincent Beauvivre <vincent@beauvivre.fr>
 * @copyright 2019
 */
class Translator
{
    const COPY_KEY = '.ac_idml_ccopy.';
    
    private $holderBuilder;

    public function __construct(
        HolderBuilder $holderBuilder
    ) {
        $this->holderBuilder = $holderBuilder;
    }
    
    public function translate(Holder $baseHolder, Project $project): Holder
    {
        if ($baseHolder->getProduct() !== $project->getProduct()) {
            throw new LogicException('Consistence error in product and project');
        }
        $copyName = self::duplicateIdml($baseHolder->getZipFileName());
        /** @var Holder $holder */
        $holder = $this->holderBuilder->buildFromProductAndPath($baseHolder->getProduct(), $copyName);
        $holder->setProject($project);
        $package = $holder->getPackage();
        foreach ($project->getProduct()->getSections() as $section) {
            $story = $holder->getStory($section->getStoryKey());
            foreach ($section->getContent() as $content) {
                foreach ($project->getContentRevision() as $revision) {
                    if ($revision->getContent() == $content) {
                        $story->setContent($section->getName(), $content->getKey(), $content->getContent());
                    }
                }
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

    private static function uniqidReal($lenght = 13)
    {
        $bytes = false;
        if (function_exists("random_bytes")) {
            $bytes = random_bytes(ceil($lenght / 2));
        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
        }

        if (!$bytes) {
            throw new Exception("no cryptographically secure random function available");
        }

        return substr(bin2hex($bytes), 0, $lenght);
    }
}
