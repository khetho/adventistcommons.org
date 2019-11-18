<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductContent
 *
 * @ORM\Table(name="product_content", indexes={@ORM\Index(name="product_id", columns={"product_id"}), @ORM\Index(name="section_id", columns={"section_id"})})
 * @ORM\Entity
 */
class ProductContent
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
     * @var bool
     *
     * @ORM\Column(name="is_hidden", type="boolean", nullable=false)
     */
    private $isHidden = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="xliff_tag", type="string", length=255, nullable=true)
     */
    private $xliffTag;

    /**
     * @var string|null
     *
     * @ORM\Column(name="content_key", type="string", length=255, nullable=true)
     */
    private $contentKey;

    /**
     * @var int|null
     *
     * @ORM\Column(name="order", type="integer", nullable=true)
     */
    private $order;

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="Product")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     * })
     */
    private $product;

    /**
     * @var ProductSections
     *
     * @ORM\ManyToOne(targetEntity="ProductSections")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="section_id", referencedColumnName="id")
     * })
     */
    private $section;

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

    public function getIsHidden(): ?bool
    {
        return $this->isHidden;
    }

    public function setIsHidden(bool $isHidden): self
    {
        $this->isHidden = $isHidden;

        return $this;
    }

    public function getXliffTag(): ?string
    {
        return $this->xliffTag;
    }

    public function setXliffTag(?string $xliffTag): self
    {
        $this->xliffTag = $xliffTag;

        return $this;
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

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getSection(): ?ProductSections
    {
        return $this->section;
    }

    public function setSection(?ProductSections $section): self
    {
        $this->section = $section;

        return $this;
    }
}
