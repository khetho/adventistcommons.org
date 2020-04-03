<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation as Api;

/**
 * ProductAttachment
 *
 * @ORM\Table(
 *     name="attachment",
 *     indexes={
 *         @ORM\Index(name="product_id", columns={"product_id"}),
 *         @ORM\Index(name="project_id", columns={"project_id"})
 *     }
 * )
 * @ORM\Entity
 * @Api\ApiResource(
 *     normalizationContext={"groups"={"normalize"}},
 * )
 */
class Attachment
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
     * @var File|null
     */
    private $file;

    /**
     * @var string|null
     *
     * @ORM\Column(name="file", type="string", length=255, nullable=true)
     * @Groups("normalize")
     */
    private $filename;

    /**
     * @var string|null
     *
     * @ORM\Column(name="file_type", type="string", length=20, nullable=true)
     * @Groups("normalize")
     */
    private $fileType;

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="attachments",cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     * })
     */
    private $product;

    /**
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="attachments",cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="project_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     * })
     */
    private $project;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getFileName(): ?string
    {
        return $this->filename;
    }

    public function setFileName(?string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getFileType(): ?string
    {
        return $this->fileType;
    }

    public function setFileType(?string $fileType): self
    {
        $this->fileType = $fileType;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product ?? $this->getProject()->getProduct();
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }
}
