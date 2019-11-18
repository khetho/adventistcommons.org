<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductAudience
 *
 * @ORM\Table(name="product_audiences", indexes={@ORM\Index(name="product_id", columns={"product_id"}), @ORM\Index(name="audience_id", columns={"audience_id"})})
 * @ORM\Entity
 */
class ProductAudience
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
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $createdAt = 'CURRENT_TIMESTAMP';

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
     * @var \Audience
     *
     * @ORM\ManyToOne(targetEntity="Audience")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="audience_id", referencedColumnName="id")
     * })
     */
    private $audience;
}
