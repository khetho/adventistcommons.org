<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Knp\DictionaryBundle\Validator\Constraints\Dictionary;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Product
 *
 * @ORM\Table(
 *     name="products",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(
 *             columns={"slug"}
 *         )
 *     },
 *     indexes={
 *         @ORM\Index(name="series_id", columns={"series_id"}),
 *         @ORM\Index(name="binding_id", columns={"binding_id"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository"))
 * @ApiResource
 * @DoctrineAssert\UniqueEntity(
 *   "name",
 *   message="A product with the same name already exists."
 * )
 * @DoctrineAssert\UniqueEntity(
 *   "slug",
 *   errorPath="name",
 *   message="A product with the same slug already exists."
 * )
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     * @Assert\Regex(
     *    pattern="/^([abcdefghijklmnopqrstuvwxyz1234567890\-]*)$/i",
     *    match=true,
     *    message="The slug include not allowed chars, allowed : letters (upper and lower case), numbers, and dash (-)"
     * )
     *
     * @ORM\Column(name="slug", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $slug;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var File|null

     * @Assert\Image(
     *     allowPortrait = false,
     *     maxSize = "1024k",
     *     allowLandscape = false,
     *     minWidth = 75,
     *     maxWidth = 1000,
     *     minHeight = 75,
     *     maxHeight = 1000
     * )
     */
    private $coverImage;
    
    /**
     * @var string|null
     *
     * @ORM\Column(name="cover_image", type="string", length=255, nullable=true)
     */
    private $coverImageFilename;

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
     * @ORM\Column(name="type", type="string", length=10, nullable=true)
     * @Dictionary(name="product_type")
     */
    private $type;

    /**
     * @var File|null
     */
    private $idmlFile;

    /**
     * @var string|null
     *
     * @ORM\Column(name="idml_file", type="string", length=255, nullable=true)
     */
    private $idmlFilename;

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
     * @var Series
     *
     * @ORM\ManyToOne(targetEntity="Series")
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
     * @var Collection
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

    /**
     * One product has many projects. This is the inverse side.
     * @ORM\OneToMany(targetEntity="Project", mappedBy="product")
     */
    private $projects;

    /**
     * One product has many projects. This is the inverse side.
     * @ORM\OneToMany(targetEntity="Section", mappedBy="product")
     */
    private $sections;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

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

    public function getCoverImage(): ?File
    {
        return $this->coverImage;
    }

    public function setCoverImage(?File $coverImage): self
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    public function getCoverImageFilename(): ?string
    {
        return $this->coverImageFilename;
    }

    public function setCoverImageFilename(?string $coverImageFilename): self
    {
        $this->coverImageFilename = $coverImageFilename;

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

    public function getIdmlFile(): ?File
    {
        return $this->idmlFile;
    }

    public function setIdmlFile(?File $idmlFile): self
    {
        $this->idmlFile = $idmlFile;

        return $this;
    }

    public function getIdmlFilename(): ?string
    {
        return $this->idmlFilename;
    }

    public function setIdmlFilename(?string $idmlFilename): self
    {
        $this->idmlFilename = $idmlFilename;

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

    public function getSeries(): ?Series
    {
        return $this->series;
    }

    public function setSeries(?Series $series): self
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
    public function getAudiences(): Collection
    {
        return $this->audiences;
    }

    public function addAudience(Audience $audience): self
    {
        if (!$this->audiences->contains($audience)) {
            $this->audiences[] = $audience;
        }

        return $this;
    }

    public function removeAudience(Audience $audience): self
    {
        if ($this->audiences->contains($audience)) {
            $this->audiences->removeElement($audience);
        }

        return $this;
    }

    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function getSections(): Collection
    {
        return $this->sections;
    }
}
