<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectContentApproval
 *
 * @ORM\Table(name="project_content_approval", indexes={@ORM\Index(name="project_id", columns={"project_id"}), @ORM\Index(name="approved_by", columns={"approved_by"}), @ORM\Index(name="content_id", columns={"content_id"})})
 * @ORM\Entity
 */
class ProjectContentApproval
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
     * @var \DateTime
     *
     * @ORM\Column(name="approved_on", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $approvedOn = 'CURRENT_TIMESTAMP';

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="approved_by", referencedColumnName="id")
     * })
     */
    private $approvedBy;

    /**
     * @var ProductContent
     *
     * @ORM\ManyToOne(targetEntity="ProductContent")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="content_id", referencedColumnName="id")
     * })
     */
    private $content;

    /**
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="Project")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     * })
     */
    private $project;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getApprovedOn(): ?\DateTimeInterface
    {
        return $this->approvedOn;
    }

    public function setApprovedOn(\DateTimeInterface $approvedOn): self
    {
        $this->approvedOn = $approvedOn;

        return $this;
    }

    public function getApprovedBy(): ?User
    {
        return $this->approvedBy;
    }

    public function setApprovedBy(?User $approvedBy): self
    {
        $this->approvedBy = $approvedBy;

        return $this;
    }

    public function getContent(): ?ProductContent
    {
        return $this->content;
    }

    public function setContent(?ProductContent $content): self
    {
        $this->content = $content;

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
