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
}
