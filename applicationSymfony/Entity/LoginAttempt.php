<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LoginAttempts
 *
 * @ORM\Table(name="login_attempts")
 * @ORM\Entity
 */
class LoginAttempt
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
     * @ORM\Column(name="ip_address", type="string", length=45, nullable=false)
     */
    private $ipAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="login", type="string", length=100, nullable=false)
     */
    private $login;

    /**
     * @var int|null
     *
     * @ORM\Column(name="time", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $time;
}
