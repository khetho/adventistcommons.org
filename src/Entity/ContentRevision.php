<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DictionaryBundle\Validator\Constraints\Dictionary;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation as Api;

/**
 * ContentRevisions
 *
 * @ORM\Table(
 *     name="product_content_revisions",
 *     indexes={
 *         @ORM\Index(name="project_id", columns={"project_id"}),
 *         @ORM\Index(name="sentence_id", columns={"sentence_id"}),
 *         @ORM\Index(name="translator_id", columns={"translator_id"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\ContentRevisionRepository"))
 * @Api\ApiResource(
 *     normalizationContext={"groups"={"normalize"}},
 * )
 */
class ContentRevision
{
    const STATUS_TRANSLATED = '';
    const STATUS_PROOFREAD = 'pro';
    const STATUS_REVIEWED = 'rev';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Groups("normalize")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="content", type="text", length=65535, nullable=true)
     * @Groups("normalize")
     */
    private $content;

    /**
     * @var string|null
     */
    private $diffContent;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     * @Groups("normalize")
     */
    private $createdAt;

    /**
     * @var Sentence
     *
     * @ORM\ManyToOne(targetEntity="Sentence")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sentence_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $sentence;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="contentRevisions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="translator_id", referencedColumnName="id")
     * })
     */
    private $translator;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="proofreader_id", referencedColumnName="id")
     * })
     */
    private $proofreader;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="reviewer_id", referencedColumnName="id")
     * })
     */
    private $reviewer;

    /**
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="contentRevisions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="project_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $project;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="text", length=3, nullable=false)
     * @Dictionary(name="content_revision_status")
     * @Groups("normalize")
     */
    private $status;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->status = self::STATUS_TRANSLATED;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getDiffContent(): ?string
    {
        return $this->diffContent;
    }

    public function setDiffContent(?string $diffContent): self
    {
        $this->diffContent = $diffContent;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getSentence(): ?Sentence
    {
        return $this->sentence;
    }

    public function setSentence(?Sentence $sentence): self
    {
        $this->sentence = $sentence;

        return $this;
    }

    public function getTranslator(): ?User
    {
        return $this->translator;
    }

    public function setTranslator(UserInterface $translator): self
    {
        $this->translator = $translator;

        return $this;
    }

    public function getProofreader(): ?User
    {
        return $this->proofreader;
    }

    public function setProofreader(UserInterface $proofreader): self
    {
        $this->proofreader = $proofreader;

        return $this;
    }

    public function getReviewer(): ?User
    {
        return $this->reviewer;
    }

    public function setReviewer(UserInterface $reviewer): self
    {
        $this->reviewer = $reviewer;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status)
    {
        $this->status = $status;
    }

    public function proofreadBy(UserInterface $proofreader)
    {
        if (!$this->getProject()->getProofreader()) {
            $this->getProject()->setProofreader($proofreader);
        }
        $this->setProofreader($proofreader);
        $this->setStatus(self::STATUS_PROOFREAD);
    }

    public function reviewBy(UserInterface $reviewer)
    {
        $this->setReviewer($reviewer);
        $this->setStatus(self::STATUS_REVIEWED);
    }

    public function isProofread()
    {
        return $this->getStatus() === self::STATUS_PROOFREAD;
    }

    public function isReviewed()
    {
        return $this->getStatus() === self::STATUS_REVIEWED;
    }
}
