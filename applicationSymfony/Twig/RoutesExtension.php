<?php

namespace App\Twig;

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
            new TwigFunction('pathToProject', [$this, 'pathToProject']),
            new TwigFunction('pathToSection', [$this, 'pathToSection']),
            new TwigFunction('pathToProjectSection', [$this, 'pathToProjectSection']),
            new TwigFunction('pathToAttachment', [$this, 'pathToAttachment']),
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
    
    public function pathToSection(Project $project, Section $section, $action = 'edit')
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
