<?php

namespace App\Twig;

use App\Entity\Product;
use App\Entity\Project;
use App\Entity\Section;
use App\Entity\Paragraph;
use App\Entity\ProjectContentApproval;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Doctrine\ORM\EntityManagerInterface;

class ProjectStatusExtension extends AbstractExtension
{
    private $manager;
    
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }
    
    public function getFilters()
    {
        return [
            new TwigFilter('projectRatioCompleted', [$this, 'projectRatioCompleted']),
            new TwigFilter('projectApprovedCount', [$this, 'projectApprovedCount']),
            new TwigFilter('productCount', [$this, 'productCount']),
            new TwigFilter('sectionCount', [$this, 'sectionCount']),
        ];
    }

    public function projectRatioCompleted(Project $project, Section $section = null)
    {
        if ($section) {
            $sectionCount = $this->sectionCount($section);
            return $sectionCount ? $this->projectApprovedCount($project, $section) / $sectionCount : null;
        }
        
        $productCount = $this->productCount($project->getProduct());
        return $productCount ? $this->projectApprovedCount($project) / $productCount : 0;
    }
    
    public function projectApprovedCount(Project $project, Section $section = null): int
    {
        return $this->manager->getRepository(Project::class)->getApprovedCount($project, $section);
    }
    
    public function productCount(Product $product): int
    {
        return $this->manager->getRepository(Product::class)->getContentCount($product);
    }
    
    public function sectionCount(Section $section): int
    {
        return $this->manager->getRepository(Section::class)->getContentCount($section);
    }
}
