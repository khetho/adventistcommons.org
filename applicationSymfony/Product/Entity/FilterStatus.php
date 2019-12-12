<?php

namespace App\Product\Entity;

use App\Entity\Audience;
use App\Entity\Binding;
use App\Entity\Series;
use Doctrine\ORM\EntityManagerInterface;
use Knp\DictionaryBundle\Validator\Constraints\Dictionary;

class FilterStatus
{
    private $title;

    private $series;

    private $audience;

    private $author;

    /**
     * @Dictionary(name="product_type")
     */
    private $type;

    private $binding;

    /**
     * @Dictionary(name="product_sort")
     */
    private $sort = 'name';

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setSeries(?Series $series): self
    {
        $this->series = $series;

        return $this;
    }

    public function getSeries(): ?Series
    {
        return $this->series;
    }

    public function setAudience(?Audience $audience): self
    {
        $this->audience = $audience;

        return $this;
    }

    public function getAudience(): ?Audience
    {
        return $this->audience;
    }

    public function setAuthor(?string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setBinding(?Binding $binding): self
    {
        $this->binding = $binding;

        return $this;
    }

    public function getBinding(): ?Binding
    {
        return $this->binding;
    }

    public function setSort($sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function getSort()
    {
        return $this->sort;
    }
    
    public function attach(EntityManagerInterface $manager): void
    {
        // Attach entities to doctrine manager so it knows they are data in db
        if ($this->getBinding()) {
            $this->setBinding(
                $manager->merge($this->getBinding())
            );
        }
        if ($this->getSeries()) {
            $this->setSeries(
                $manager->merge($this->getSeries())
            );
        }
        if ($audience = $this->getAudience()) {
            $this->setAudience(
                $manager->merge($audience)
            );
        }
    }
}
