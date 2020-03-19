<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Knp\DictionaryBundle\Validator\Constraints\Dictionary;

/**
 * ContentRevisions
 *
 * @ORM\Table(
 *     name="product_content_revisions",
 *     indexes={
 *         @ORM\Index(name="project_id", columns={"project_id"}),
 *         @ORM\Index(name="sentence_id", columns={"sentence_id"}),
 *         @ORM\Index(name="user_id", columns={"user_id"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\ContentRevisionRepository"))
 */
class ContentRevision
{
    const STATUS_TRANSLATED = '';
    const STATUS_APPROVED = 'app';
    const STATUS_REVIEWED = 'rev';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="content", type="text", length=65535, nullable=true)
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
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

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

    public function getSentence(): ?Paragraph
    {
        return $this->sentence;
    }

    public function setSentence(?Sentence $sentence): self
    {
        $this->sentence = $sentence;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;

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

    public function approve()
    {
        $this->setStatus(self::STATUS_APPROVED);
    }

    public function review()
    {
        $this->setStatus(self::STATUS_REVIEWED);
    }

    public function isApproved()
    {
        return $this->getStatus() === self::STATUS_APPROVED;
    }

    public function isReviewed()
    {
        return $this->getStatus() === self::STATUS_REVIEWED;
    }
}
