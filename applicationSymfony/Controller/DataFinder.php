<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Project;
use App\Entity\Language;
use App\Entity\Section;
use App\Entity\Attachment;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DataFinder
{
    private $registry;
    
    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->registry	= $managerRegistry;
    }
    
    public function retrieveProjectOr404($slug, $languageCode): Project
    {
        $product = $this->retrieveProductOr404($slug);
        $language = $this->retrieveLanguageOr404($languageCode);
        /** @var Project $project */
        $project = $this->registry->getRepository(Project::class)->findOneBy([
            'language' => $language,
            'product' => $product,
        ]);
        
        if (!$project) {
            throw new NotFoundHttpException();
        }
        
        return $project;
    }

    public function retrieveProductOr404(string $slug): Product
    {
        /** @var Product $product */
        $product = $this->registry->getRepository(Product::class)->findOneBy([
            'slug' => $slug,
        ]);
        if (!$product) {
            throw new NotFoundHttpException();
        }

        return $product;
    }

    public function retrieveLanguageOr404(string $code): Language
    {
        /** @var Language $language */
        $language = $this->registry->getRepository(Language::class)->findOneBy([
            'code' => $code,
        ]);
        if (!$language) {
            throw new NotFoundHttpException();
        }

        return $language;
    }

    public function retrieveSectionOr404($slug, $sectionName): Section
    {
        $product = $this->retrieveProductOr404($slug);
        /** @var Section $section */
        $section = $this->registry->getRepository(Section::class)->findOneBy([
            'product' => $product,
            'name' => $sectionName,
        ]);
        if (!$section) {
            throw new NotFoundHttpException();
        }

        return $section;
    }
    
    public function retrieveAttachmentOr404($slug, $languageCode, $id): Attachment
    {
        $project = $this->retrieveProjectOr404($slug, $languageCode);
        /** @var Attachment $attachment */
        $attachment = $this->registry->getRepository(Attachment::class)->find($id);
        if (!$attachment || ($attachment->getProject()->getId() !== $project->getId())) {
            throw new NotFoundHttpException();
        }

        return $attachment;
    }
}
