<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Project;
use App\Entity\Language;
use App\Entity\Section;
use App\Entity\Attachment;
use App\Entity\ContentRevision;
use App\Entity\Sentence;
use App\Twig\RoutesExtension;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class DataFinder
{
    private $registry;
    private $security;
    private $breadcrumbs;
    private $appRouter;

    public function __construct(
        ManagerRegistry $managerRegistry,
        Security $security,
        Breadcrumbs $breadcrumbs,
        RoutesExtension $appRouter
    ) {
        $this->registry = $managerRegistry;
        $this->security = $security;
        $this->breadcrumbs = $breadcrumbs;
        $this->appRouter = $appRouter;
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
        if (!$project->isEnabled() && !$this->security->isGranted('ROLE_ADMIN')) {
            throw new NotFoundHttpException();
        }
        $this->breadcrumbs->addItem(
            'commons.breadcrumbs.project',
            $this->appRouter->pathToProject($project),
            ['%language%' => $project->getLanguage()->getName()]
        );

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
        if (!$product->isEnabled() && !$this->security->isGranted('ROLE_ADMIN')) {
            throw new NotFoundHttpException();
        }
        $this->breadcrumbs->addItem(
            'commons.breadcrumbs.products',
            $this->appRouter->pathToProducts()
        );
        $this->breadcrumbs->addItem(
            'commons.breadcrumbs.product',
            $this->appRouter->pathToProduct($product),
            ['%product%' => $product->getName()]
        );

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

    public function retrieveSectionOr404($sectionName, Project $project): Section
    {
        /** @var Section $section */
        $section = $this->registry->getRepository(Section::class)->findOneBy([
            'product' => $project->getProduct(),
            'name' => $sectionName,
        ]);
        if (!$section) {
            throw new NotFoundHttpException();
        }
        if ($project) {
            $this->breadcrumbs->addItem(
                'commons.breadcrumbs.section',
                $this->appRouter->pathToSection($project, $section),
                ['%section%' => $section->getName()]
            );
        }

        return $section;
    }

    public function addLatestTranslations(Section $section, Project $project): Section
    {
        $transBySentenceId = $this->registry
            ->getRepository(ContentRevision::class)
            ->getLatestForProjectAndSection($project, $section);
        foreach ($section->getParagraphs() as $paragraph) {
            /** @var Sentence $sentence */
            foreach ($paragraph->getSentences() as $sentence) {
                if (isset($transBySentenceId[$sentence->getId()])) {
                    $sentence->setTranslation($transBySentenceId[$sentence->getId()]);
                }
            }
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

    public function retrieveSentenceOr404($sentenceId, Project $project): Sentence
    {
        $sentence = $this->registry->getRepository(Sentence::class)->find($sentenceId);
        if (!$sentence || (
            $sentence->getParagraph()->getSection()->getProduct()->getId() !== $project->getProduct()->getId()
        )) {
            throw new NotFoundHttpException();
        }

        return $sentence;
    }

    public function retrieveRevisions($sentence)
    {
        return $this->registry
            ->getRepository(ContentRevision::class)
            ->findBySentence($sentence);
    }

    public function retrieveSentenceInfoOr404($slug, $languageCode, $sentenceId)
    {
        $project = $this->retrieveProjectOr404($slug, $languageCode);
        $sentence = $this->retrieveSentenceOr404($sentenceId, $project);

        return $this->registry->getRepository(Sentence::class)->getSentenceInfo($project, $sentence);
    }

    public function retrieveLatestContentRevisionOr404($slug, $languageCode, $sentenceId)
    {
        $project = $this->retrieveProjectOr404($slug, $languageCode);
        $sentence = $this->retrieveSentenceOr404($sentenceId, $project);
        $contentRevision = $this->registry
            ->getRepository(ContentRevision::class)
            ->findLatestForSentenceAndProject($sentence, $project);

        if (!$contentRevision) {
            throw new NotFoundHttpException();
        }

        return $contentRevision;
    }
}
