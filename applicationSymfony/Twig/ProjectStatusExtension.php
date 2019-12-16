<?php

namespace App\Twig;

use App\Entity\Product;
use App\Entity\Project;
use App\Entity\Section;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ProjectStatusExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('projectRatioCompleted', [$this, 'projectRatioCompleted']),
            new TwigFilter('projectCountCompleted', [$this, 'projectCountCompleted']),
            new TwigFilter('productCount', [$this, 'productCount']),
            new TwigFilter('projectRouteParams', [$this, 'projectRouteParams']),
            new TwigFilter('sectionRouteParams', [$this, 'sectionRouteParams']),
        ];
    }

    /**
     * @TODO
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function projectRatioCompleted(Project $project, Section $section = null)
    {
        return .64;
    }
    
    /**
     * @TODO
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function projectCountCompleted(Project $project, Section $section = null)
    {
        return 64;
    }
    
    /**
     * @TODO
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function productCount(Product $product, Section $section = null)
    {
        return 168;
    }
    
    public function projectRouteParams(Project $project)
    {
        return [
            'slug' => $project->getProduct()->getSlug(),
            'languageCode' => $project->getLanguage()->getCode(),
        ];
    }
    
    public function sectionRouteParams(Section $section, Project $project)
    {
        return array_merge(
            $this->projectRouteParams($project),
            [
                'sectionName' => $section->getName(),
            ]
        );
    }
}
