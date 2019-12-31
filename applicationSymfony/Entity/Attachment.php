<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

/**
 * ProjectAttachment
 *
 * @ORM\Table(
 *     name="product_attachments",
 *     indexes={
 *         @ORM\Index(name="project_id", columns={"project_id"})
 *     }
 * )
 * @ORM\Entity
 */
class Attachment
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
     * @var File|null
     */
    private $file;

    /**
     * @var string|null
     *
     * @ORM\Column(name="file", type="string", length=255, nullable=true)
     */
    private $filename;

    /**
     * @var string|null
     *
     * @ORM\Column(name="file_type", type="string", length=0, nullable=true)
     */
    private $fileType;

    /**
     * @var Language
     */
    private $language;

    /**
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="attachments",cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="project_id", referencedColumnName="id")
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

    public function getLanguage(): ?Language
    {
        return $this->language ?? $this->getProject()->getLanguage();
    }

    public function setLanguage(?Language $language): self
    {
        $this->language = $language;

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
