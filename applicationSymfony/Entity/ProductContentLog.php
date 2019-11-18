<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductContentLog
 *
 * @ORM\Table(name="product_content_log", indexes={@ORM\Index(name="project_id", columns={"project_id"}), @ORM\Index(name="content_id", columns={"content_id"}), @ORM\Index(name="resolved_by", columns={"resolved_by"}), @ORM\Index(name="user_id", columns={"user_id"})})
 * @ORM\Entity
 */
class ProductContentLog
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
     * @ORM\Column(name="comment", type="text", length=65535, nullable=true)
     */
    private $comment;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $createdAt = 'CURRENT_TIMESTAMP';

    /**
     * @var string|null
     *
     * @ORM\Column(name="type", type="string", length=0, nullable=true)
     */
    private $type;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_resolved", type="boolean", nullable=false)
     */
    private $isResolved;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="resolved_on", type="datetime", nullable=true)
     */
    private $resolvedOn;

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

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="resolved_by", referencedColumnName="id")
     * })
     */
    private $resolvedBy;
}
