<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductSections
 *
 * @ORM\Table(name="product_sections", indexes={@ORM\Index(name="product_id", columns={"product_id"})})
 * @ORM\Entity
 */
class ProductSections
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
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="order", type="integer", nullable=false)
     */
    private $order = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer", nullable=false)
     */
    private $position = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="style", type="string", length=255, nullable=true)
     */
    private $style;

    /**
     * @var string|null
     *
     * @ORM\Column(name="node_id", type="string", length=255, nullable=true)
     */
    private $nodeId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="xliff_region", type="string", length=255, nullable=true)
     */
    private $xliffRegion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="story_key", type="string", length=255, nullable=true)
     */
    private $storyKey;

    /**
     * @var \Product
     *
     * @ORM\ManyToOne(targetEntity="Product")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     * })
     */
    private $product;
}
