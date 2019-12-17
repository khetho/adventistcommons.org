<?php

namespace App\Twig;

use App\Entity\Project;
use App\Entity\Section;
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
            new TwigFunction('pathToProject', [$this, 'pathToProject']),
            new TwigFunction('pathToProjectSection', [$this, 'pathToProjectSection']),
        ];
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
    
    public function pathToProjectSection(Project $project, Section $section, $action = 'edit')
    {
        return $this->router->generate(
            sprintf('app_project_%s', $action),
            [
                'slug' => $project->getProduct()->getSlug(),
                'languageCode' => $project->getLanguage()->getCode(),
                'sectionName' => $section->getName(),
            ]
        );
    }
}
