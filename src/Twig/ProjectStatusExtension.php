<?php

namespace App\Twig;

use App\Entity\ContentRevision;
use App\Entity\Product;
use App\Entity\Project;
use App\Entity\Section;
use App\Entity\Sentence;
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
            new TwigFilter('projectTranslatedRatio', [$this, 'projectTranslatedRatio']),
            new TwigFilter('projectApprovedRatio', [$this, 'projectApprovedRatio']),
            new TwigFilter('projectReviewedRatio', [$this, 'projectReviewedRatio']),
            new TwigFilter('projectTranslatedCount', [$this, 'projectTranslatedCount']),
            new TwigFilter('projectApprovedCount', [$this, 'projectApprovedCount']),
            new TwigFilter('projectReviewedCount', [$this, 'projectReviewedCount']),
            new TwigFilter('sentenceCountForProduct', [$this, 'sentenceCountForProduct']),
            new TwigFilter('sentenceCountForSection', [$this, 'sentenceCountForSection']),
        ];
    }

    private function getTotal(Project $project, Section $section = null)
    {
        return $section
            ? $this->sentenceCountForSection($section)
            : $this->sentenceCountForProduct($project->getProduct());
    }

    public function projectTranslatedRatio(Project $project, Section $section = null)
    {
        $totalCount = $this->getTotal($project, $section);
        return $totalCount ? $this->projectTranslatedCount($project, $section) / $totalCount : 0;
    }

    public function projectApprovedRatio(Project $project, Section $section = null)
    {
        $totalCount = $this->getTotal($project, $section);
        return $totalCount ? $this->projectApprovedCount($project, $section) / $totalCount : 0;
    }

    public function projectReviewedRatio(Project $project, Section $section = null)
    {
        $totalCount = $this->getTotal($project, $section);
        return $totalCount ? $this->projectReviewedCount($project, $section) / $totalCount : 0;
    }

    public function projectTranslatedCount(Project $project, Section $section = null): int
    {
        return $this->manager->getRepository(ContentRevision::class)->getCountForStatus($project, ContentRevision::STATUS_TRANSLATED, $section);
    }

    public function projectApprovedCount(Project $project, Section $section = null): int
    {
        return $this->manager->getRepository(ContentRevision::class)->getCountForStatus($project, ContentRevision::STATUS_APPROVED, $section);
    }

    public function projectReviewedCount(Project $project, Section $section = null): int
    {
        return $this->manager->getRepository(ContentRevision::class)->getCountForStatus($project, ContentRevision::STATUS_REVIEWED, $section);
    }

    public function sentenceCountForProduct(Product $product): int
    {
        return $this->manager->getRepository(Sentence::class)->getCountForProduct($product);
    }
    
    public function sentenceCountForSection(Section $section): int
    {
        return $this->manager->getRepository(Sentence::class)->getCountForSection($section);
    }
}
