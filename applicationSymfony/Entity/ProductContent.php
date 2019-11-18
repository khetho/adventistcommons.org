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
     * @var \Product
     *
     * @ORM\ManyToOne(targetEntity="Product")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     * })
     */
    private $product;

    /**
     * @var \ProductSections
     *
     * @ORM\ManyToOne(targetEntity="ProductSections")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="section_id", referencedColumnName="id")
     * })
     */
    private $section;
}
