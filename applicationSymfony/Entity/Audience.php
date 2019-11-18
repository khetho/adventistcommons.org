<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Audience
 *
 * @ORM\Table(name="audiences", uniqueConstraints={@ORM\UniqueConstraint(name="uc_name", columns={"name"})})
 * @ORM\Entity
 */
class Audience
{
    /**
     * @var bool
     *
     * @ORM\Column(name="id", type="boolean", nullable=false)
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
}
