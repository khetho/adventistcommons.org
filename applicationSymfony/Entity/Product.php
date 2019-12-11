<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Knp\DictionaryBundle\Validator\Constraints\Dictionary;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Product
 *
 * @ORM\Table(
 *     name="products",
 *     indexes={
 *         @ORM\Index(name="series_id", columns={"series_id"}),
 *         @ORM\Index(name="binding_id", columns={"binding_id"})
 *     }
 * )
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
     * @Assert\NotBlank()
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
     * @Dictionary(name="product_type")
     */
    private $type;

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
     * @var Serie
     *
     * @ORM\ManyToOne(targetEntity="Serie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="series_id", referencedColumnName="id")
     * })
     */
    private $series;

    /**
     * @var Binding
     *
     * @ORM\ManyToOne(targetEntity="Binding")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="binding_id", referencedColumnName="id")
     * })
     */
    private $binding;

    /**
     * @var ArrayCollection
     *
     * Many Product have Many Audience.
     * @ORM\ManyToMany(targetEntity="Audience")
     * @ORM\JoinTable(
     *      name="product_audiences",
     *      joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="audience_id", referencedColumnName="id")}
     * )
     */
    private $audiences;

    public function __construct()
    {
        $this->audiences = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(?string $coverImage): self
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(?string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getPageCount(): ?int
    {
        return $this->pageCount;
    }

    public function setPageCount(?int $pageCount): self
    {
        $this->pageCount = $pageCount;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getIdmlFile(): ?string
    {
        return $this->idmlFile;
    }

    public function setIdmlFile(?string $idmlFile): self
    {
        $this->idmlFile = $idmlFile;

        return $this;
    }

    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function setPublisher(?string $publisher): self
    {
        $this->publisher = $publisher;

        return $this;
    }

    public function getFormatOpen(): ?string
    {
        return $this->formatOpen;
    }

    public function setFormatOpen(?string $formatOpen): self
    {
        $this->formatOpen = $formatOpen;

        return $this;
    }

    public function getFormatClosed(): ?string
    {
        return $this->formatClosed;
    }

    public function setFormatClosed(?string $formatClosed): self
    {
        $this->formatClosed = $formatClosed;

        return $this;
    }

    public function getCoverColors(): ?string
    {
        return $this->coverColors;
    }

    public function setCoverColors(?string $coverColors): self
    {
        $this->coverColors = $coverColors;

        return $this;
    }

    public function getCoverPaper(): ?string
    {
        return $this->coverPaper;
    }

    public function setCoverPaper(?string $coverPaper): self
    {
        $this->coverPaper = $coverPaper;

        return $this;
    }

    public function getInteriorColors(): ?string
    {
        return $this->interiorColors;
    }

    public function setInteriorColors(?string $interiorColors): self
    {
        $this->interiorColors = $interiorColors;

        return $this;
    }

    public function getInteriorPaper(): ?string
    {
        return $this->interiorPaper;
    }

    public function setInteriorPaper(?string $interiorPaper): self
    {
        $this->interiorPaper = $interiorPaper;

        return $this;
    }

    public function getFinishing(): ?string
    {
        return $this->finishing;
    }

    public function setFinishing(?string $finishing): self
    {
        $this->finishing = $finishing;

        return $this;
    }

    public function getPublisherWebsite(): ?string
    {
        return $this->publisherWebsite;
    }

    public function setPublisherWebsite(?string $publisherWebsite): self
    {
        $this->publisherWebsite = $publisherWebsite;

        return $this;
    }

    public function getSeries(): ?Serie
    {
        return $this->series;
    }

    public function setSeries(?Serie $series): self
    {
        $this->series = $series;

        return $this;
    }

    public function getBinding(): ?Binding
    {
        return $this->binding;
    }

    public function setBinding(?Binding $binding): self
    {
        $this->binding = $binding;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getAudiences(): ArrayCollection
    {
        return $this->audiences;
    }

    public function addAudience(Audience $Audience): self
    {
        if (!$this->audiences->contains($Audience)) {
            $this->audiences[] = $Audience;
        }

        return $this;
    }

    public function removeAudience(Audience $Audience): self
    {
        if ($this->audiences->contains($Audience)) {
            $this->audiences->removeElement($Audience);
        }

        return $this;
    }
}
