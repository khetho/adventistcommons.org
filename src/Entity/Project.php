<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Project
 *
 * @ORM\Table(
 *     name="projects",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(
 *             columns={"product_id", "language_id"}
 *         )
 *     },
 *     indexes={
 *         @ORM\Index(name="product_id", columns={"product_id"}),
 *         @ORM\Index(name="language_id", columns={"language_id"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository"))
 * @ApiResource()
 */
class Project
{
    const STATUS_TRANSLATABLE = 'translatable'; // translation can be started
    const STATUS_STARTED = 'started'; // translation has started
    const STATUS_TRANSLATED = 'translated'; // all content is translated
    const STATUS_PROOFREAD = 'proofread'; // all content is proofread
    const STATUS_REVIEWED = 'reviewed'; // all content is reviewed
    const STATUS_DOWNLOADABLE = 'downloadable'; // the idml as been rearranged in  that language, and a pdf of it uploaded

    const TRANSITION_START = 'start';
    const TRANSITION_DECLARE_TRANSLATED = 'declare_translated';
    const TRANSITION_DECLARE_PROOFREAD = 'declare_proofread';
    const TRANSITION_DECLARE_REVIEWED = 'declare_reviewed';
    const TRANSITION_UPLOAD_RESULT = 'upload_result';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", nullable=false, length=16)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     */
    private $enabled;

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="projects")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $product;

    /**
     * @var Language
     *
     * @ORM\ManyToOne(targetEntity="Language", inversedBy="projects")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="language_id", referencedColumnName="id")
     * })
     */
    private $language;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="projects")
     * @ORM\JoinTable(
     *      name="project_user",
     *      joinColumns={@ORM\JoinColumn(name="project_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     * )
     */
    private $members;

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
     * One product has many projects. This is the inverse side.
     * @ORM\OneToMany(targetEntity="Attachment", mappedBy="project")
     */
    private $attachments;
    
    /**
     * @ORM\OneToMany(targetEntity="ContentRevision", mappedBy="project")
     */
    private $contentRevisions;

    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->attachments = new ArrayCollection();
        $this->enable();
        $this->setStatus(self::STATUS_TRANSLATABLE);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function setLanguage(?Language $language): self
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(User $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
        }

        return $this;
    }

    public function removeMember(User $member): self
    {
        if ($this->members->contains($member)) {
            $this->members->removeElement($member);
        }

        return $this;
    }
    
    public function getContentRevisions()
    {
        return $this->contentRevisions;
    }

    public function getAttachments(): Collection
    {
        return $this->attachments;
    }

    public function getProofreader(): ?User
    {
        return $this->proofreader;
    }

    public function setProofreader(?UserInterface $proofreader): self
    {
        $this->proofreader = $proofreader;

        return $this;
    }

    public function getReviewer(): ?User
    {
        return $this->reviewer;
    }

    public function setReviewer(?UserInterface $reviewer): self
    {
        $this->reviewer = $reviewer;

        return $this;
    }

    public function isReviewed()
    {
        return in_array($this->getStatus(), [self::STATUS_REVIEWED, self::STATUS_DOWNLOADABLE]);
    }

    public function isStarted()
    {
        return $this->getStatus() == self::STATUS_STARTED;
    }

    public function isDownloadable()
    {
        return $this->getStatus() == self::STATUS_DOWNLOADABLE;
    }

    public function enable()
    {
        $this->enabled = true;
    }

    public function disable()
    {
        $this->enabled = false;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }
}
