<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * Product
 *
 * @ORM\Table(name="products", indexes={@ORM\Index(name="series_id", columns={"series_id"}), @ORM\Index(name="binding", columns={"binding"})})
 * @ORM\Entity
 * @ApiResource
 */
class Product
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
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cover_image", type="string", length=255, nullable=true)
     */
    private $coverImage;

    /**
     * @var string|null
     *
     * @ORM\Column(name="author", type="string", length=255, nullable=true)
     */
    private $author;

    /**
     * @var int|null
     *
     * @ORM\Column(name="page_count", type="integer", nullable=true)
     */
    private $pageCount;

    /**
     * @var string|null
     *
     * @ORM\Column(name="type", type="string", length=0, nullable=true, options={"default"="book"})
     */
    private $type = 'book';

    /**
     * @var string|null
     *
     * @ORM\Column(name="idml_file", type="string", length=255, nullable=true)
     */
    private $idmlFile;

    /**
     * @var string|null
     *
     * @ORM\Column(name="publisher", type="string", length=255, nullable=true)
     */
    private $publisher;

    /**
     * @var string|null
     *
     * @ORM\Column(name="format_open", type="string", length=32, nullable=true)
     */
    private $formatOpen;

    /**
     * @var string|null
     *
     * @ORM\Column(name="format_closed", type="string", length=32, nullable=true)
     */
    private $formatClosed;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cover_colors", type="string", length=32, nullable=true)
     */
    private $coverColors;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cover_paper", type="string", length=32, nullable=true)
     */
    private $coverPaper;

    /**
     * @var string|null
     *
     * @ORM\Column(name="interior_colors", type="string", length=32, nullable=true)
     */
    private $interiorColors;

    /**
     * @var string|null
     *
     * @ORM\Column(name="interior_paper", type="string", length=32, nullable=true)
     */
    private $interiorPaper;

    /**
     * @var string|null
     *
     * @ORM\Column(name="finishing", type="string", length=32, nullable=true)
     */
    private $finishing;

    /**
     * @var string|null
     *
     * @ORM\Column(name="publisher_website", type="string", length=255, nullable=true)
     */
    private $publisherWebsite;

    /**
     * @var \Serie
     *
     * @ORM\ManyToOne(targetEntity="Serie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="series_id", referencedColumnName="id")
     * })
     */
    private $series;

    /**
     * @var \ProductBindings
     *
     * @ORM\ManyToOne(targetEntity="ProductBindings")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="binding", referencedColumnName="id")
     * })
     */
    private $binding;
}
