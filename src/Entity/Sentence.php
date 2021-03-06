<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation as Api;

/**
 * Content
 *
 * @ORM\Table(
 *     indexes={
 *         @ORM\Index(name="paragraph_id", columns={"paragraph_id"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\SentenceRepository"))
 * @Api\ApiResource(
 *     normalizationContext={"groups"={"normalize"}},
 * )
 */
class Sentence
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
     * @ORM\Column(name="content", type="text", length=65535, nullable=true)
     * @Groups("normalize")
     */
    private $content;

    /**
     * @var int|null
     *
     * @ORM\Column(name="`order`", type="integer", nullable=true)
     * @Groups("normalize")
     */
    private $order;

    /**
     * @var Paragraph
     *
     * @ORM\ManyToOne(targetEntity="Paragraph", inversedBy="sentences")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="paragraph_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $paragraph;
    
    /**
     * @var ContentRevision
     */
    private $translation;

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

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function setOrder(?int $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function getParagraph(): ?Paragraph
    {
        return $this->paragraph;
    }

    public function setParagraph(?Paragraph $paragraph): self
    {
        $this->paragraph = $paragraph;

        return $this;
    }
    
    public function setTranslation(ContentRevision $translation): self
    {
        $this->translation = $translation;
 
        return $this;
    }
    
    public function getTranslation(): ?ContentRevision
    {
        return $this->translation;
    }
}
