<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Language
 *
 * @ORM\Table(name="languages")
 * @ORM\Entity
 */
class Language
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
     * @ORM\Column(name="code", type="string", length=3, nullable=true)
     */
    private $code;

    /**
     * @var string|null
     *
     * @ORM\Column(name="google_code", type="string", length=8, nullable=true)
     */
    private $googleCode;

    /**
     * @var bool
     *
     * @ORM\Column(name="rtl", type="boolean", nullable=false)
     */
    private $rtl;

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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getGoogleCode(): ?string
    {
        return $this->googleCode;
    }

    public function setGoogleCode(?string $googleCode): self
    {
        $this->googleCode = $googleCode;

        return $this;
    }

    public function getRtl(): ?bool
    {
        return $this->rtl;
    }

    public function setRtl(bool $rtl): self
    {
        $this->rtl = $rtl;

        return $this;
    }
}
