<?php

namespace App\Product\Entity;

use App\Entity\Audience;
use App\Entity\Binding;
use App\Entity\Serie;

class FilterStatus
{
    private $title;

    private $serie;

    private $audience;

    private $author;

    private $type;

    private $binding;

    private $sort;

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setSerie(Serie $serie): self
    {
        $this->serie = $serie;

        return $this;
    }

    public function getSerie(): Serie
    {
        return $this->serie;
    }

    public function setAudience(Audience $audience): self
    {
        $this->audience = $audience;

        return $this;
    }

    public function getaudience(): Audience
    {
        return $this->audience;
    }

    public function setauthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getauthor(): string
    {
        return $this->author;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function gettype(): string
    {
        return $this->type;
    }

    public function setBinding(Binding $binding): self
    {
        $this->binding = $binding;

        return $this;
    }

    public function getbinding(): Binding
    {
        return $this->binding;
    }

    public function setSort(string $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function getsort(): string
    {
        return $this->sort;
    }
}
