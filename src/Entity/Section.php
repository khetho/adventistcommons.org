<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation as Api;

/**
 * Section
 *
 * @ORM\Table(
 *     name="product_sections",
 *     indexes={
 *         @ORM\Index(name="product_id", columns={"product_id"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\SectionRepository"))
 * @Api\ApiResource(
 *     normalizationContext={"groups"={"normalize"}},
 * )
 */
class Section
{
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
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     * @Groups("normalize")
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="`order`", type="integer", nullable=false)
     * @Groups("normalize")
     */
    private $order = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer", nullable=false)
     * @Groups("normalize")
     */
    private $position = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="style", type="string", length=255, nullable=true)
     * @Groups("normalize")
     */
    private $style;

    /**
     * @var string|null
     *
     * @ORM\Column(name="node_id", type="string", length=255, nullable=true)
     * @Groups("normalize")
     */
    private $nodeId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="xliff_region", type="string", length=255, nullable=true)
     * @Groups("normalize")
     */
    private $xliffRegion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="story_key", type="string", length=255, nullable=true)
     * @Groups("normalize")
     */
    private $storyKey;

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="sections")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $product;

    /**
     * One product has many projects. This is the inverse side.
     * @ORM\OneToMany(targetEntity="Paragraph", mappedBy="section")
     * @Groups("normalize")
     */
    private $paragraphs;

    public function __construct()
    {
        $this->paragraphs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function setOrder(int $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getStyle(): ?string
    {
        return $this->style;
    }

    public function setStyle(?string $style): self
    {
        $this->style = $style;

        return $this;
    }

    public function getNodeId(): ?string
    {
        return $this->nodeId;
    }

    public function setNodeId(?string $nodeId): self
    {
        $this->nodeId = $nodeId;

        return $this;
    }

    public function getXliffRegion(): ?string
    {
        return $this->xliffRegion;
    }

    public function setXliffRegion(?string $xliffRegion): self
    {
        $this->xliffRegion = $xliffRegion;

        return $this;
    }

    public function getStoryKey(): ?string
    {
        return $this->storyKey;
    }

    public function setStoryKey(?string $storyKey): self
    {
        $this->storyKey = $storyKey;

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
    
    public function getParagraphs()
    {
        return $this->paragraphs;
    }
}
