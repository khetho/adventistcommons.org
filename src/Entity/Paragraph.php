<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Content
 *
 * @ORM\Table(
 *     indexes={
 *         @ORM\Index(name="section_id", columns={"section_id"})
 *     }
 * )
 * @ORM\Entity
 */
class Paragraph
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
     * @ORM\Column(name="content_key", type="string", length=255, nullable=true)
     */
    private $contentKey;

    /**
     * @var int|null
     *
     * @ORM\Column(name="`order`", type="integer", nullable=true)
     */
    private $order;

    /**
     * @var Section
     *
     * @ORM\ManyToOne(targetEntity="Section", inversedBy="paragraphs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="section_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $section;

    /**
     * @ORM\OneToMany(targetEntity="Sentence", mappedBy="paragraph")
     */
    private $sentences;

    public function __construct()
    {
        $this->sentences = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContentKey(): ?string
    {
        return $this->contentKey;
    }

    public function setContentKey(?string $contentKey): self
    {
        $this->contentKey = $contentKey;

        return $this;
    }

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function setOrder(?int $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function getSection(): ?Section
    {
        return $this->section;
    }

    public function setSection(?Section $section): self
    {
        $this->section = $section;

        return $this;
    }

    public function getSentences()
    {
        return $this->sentences;
    }
}
