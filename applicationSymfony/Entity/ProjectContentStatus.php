<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectContentStatus
 *
 * @ORM\Table(name="project_content_status", indexes={@ORM\Index(name="content_id", columns={"content_id"}), @ORM\Index(name="project_id", columns={"project_id"})})
 * @ORM\Entity
 */
class ProjectContentStatus
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
     * @var bool
     *
     * @ORM\Column(name="is_approved", type="boolean", nullable=false)
     */
    private $isApproved;

    /**
     * @var \ProductContent
     *
     * @ORM\ManyToOne(targetEntity="ProductContent")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="content_id", referencedColumnName="id")
     * })
     */
    private $content;

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
