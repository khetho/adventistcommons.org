<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectMembers
 *
 * @ORM\Table(
 *     name="project_members",
 *     indexes={
 *         @ORM\Index(name="user_id", columns={"user_id"}),
 *         @ORM\Index(name="project_id", columns={"project_id"})
 *     }
 * )
 * @ORM\Entity
 */
class ProjectMember
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
     * @ORM\Column(name="type", type="string", length=0, nullable=true)
     */
    private $type;

    /**
     * @var string|null
     *
     * @ORM\Column(name="invite_email", type="string", length=255, nullable=true)
     */
    private $inviteEmail;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var \Project
     *
     * @ORM\ManyToOne(targetEntity="Project")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     * })
     */
    private $project;
}
