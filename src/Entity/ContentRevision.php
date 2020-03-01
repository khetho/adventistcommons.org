<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $createdAt;

    /**
     * @var Paragraph
     *
     * @ORM\ManyToOne(targetEntity="Sentence")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sentence_id", referencedColumnName="id")
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
     *   @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     * })
     */
    private $project;
    
    public function __construct()
    {
        $this->createdAt = new \DateTime();
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

    public function setUser(?User $user): self
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
}
