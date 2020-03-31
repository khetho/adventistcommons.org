<?php

namespace App\Twig;

use App\Entity\Product;
use App\Entity\Project;
use App\Entity\Section;
use App\Entity\Attachment;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RoutesExtension extends AbstractExtension
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('pathToProduct', [$this, 'pathToProduct']),
            new TwigFunction('pathToProject', [$this, 'pathToProject']),
            new TwigFunction('pathToSection', [$this, 'pathToSection']),
            new TwigFunction('pathToPreviousSection', [$this, 'pathToPreviousSection']),
            new TwigFunction('pathToNextSection', [$this, 'pathToNextSection']),
            new TwigFunction('pathToProjectSection', [$this, 'pathToProjectSection']),
            new TwigFunction('pathToAttachment', [$this, 'pathToAttachment']),
        ];
    }

    public function pathToProduct(Product $product, $action = 'show')
    {
        return $this->router->generate(
            sprintf('app_product_%s', $action),
            [
                'slug' => $product->getSlug(),
            ]
        );
    }

    public function pathToProject(Project $project, $action = 'show')
    {
        return $this->router->generate(
            sprintf('app_project_%s', $action),
            [
                'slug' => $project->getProduct()->getSlug(),
                'languageCode' => $project->getLanguage()->getCode(),
            ]
        );
    }
    
    public function pathToSection(Project $project, Section $section, $action = 'edit')
    {
        return $this->router->generate(
            sprintf('app_section_%s', $action),
            [
                'slug' => $project->getProduct()->getSlug(),
                'languageCode' => $project->getLanguage()->getCode(),
                'sectionName' => $section->getName(),
            ]
        );
    }

    public function pathToPreviousSection(Project $project, Section $section, $action = 'edit')
    {
        $prevSection = null;
        foreach ($project->getProduct()->getSections() as $otherSection) {
            if ($otherSection == $section) {
                break;
            }
            $prevSection = $otherSection;
        }
        return $prevSection ? $this->pathToSection($project, $prevSection, $action) : null;
    }

    public function pathToNextSection(Project $project, Section $section, $action = 'edit')
    {
        $nextSection = null;
        $next = false;
        foreach ($project->getProduct()->getSections() as $otherSection) {
            if ($next) {
                $nextSection = $otherSection;
                break;
            }
            if ($otherSection == $section) {
                $next = true;
            }
        }
        return $nextSection ? $this->pathToSection($project, $nextSection, $action) : null;
    }

    public function pathToAttachment(Attachment $attachment)
    {
        return $this->router->generate(
            'app_attachment_download',
            [
                'slug' => $attachment->getProject()->getProduct()->getSlug(),
                'languageCode' => $attachment->getProject()->getLanguage()->getCode(),
                'id' => $attachment->getId(),
            ]
        );
    }
}
