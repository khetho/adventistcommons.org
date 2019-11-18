<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Audience
 *
 * @ORM\Table(
 *     name="product_audiences",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="uc_name", columns={"name"})
 *     }
 * )
 * @ORM\Entity
 */
class Audience
{
    /**
     * @var bool
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
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

    public function getId(): ?bool
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
}
