<?php

namespace App\Product\Filter;

use App\Entity\Audience;
use App\Entity\Binding;
use App\Entity\Language;
use Doctrine\ORM\EntityManagerInterface;
use Knp\DictionaryBundle\Validator\Constraints\Dictionary;

class FilterStatus
{
    /** @var string */
    private $title;

    /** @var Language */
    private $language;

    /** @var Audience */
    private $audience;

    /** @var string */
    private $author;

    /** @Dictionary(name="product_type") */
    private $type;

    /** @var Binding */
    private $binding;

    /** @Dictionary(name="product_sort") */
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

    public function setLanguage(?Language $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getLanguage(): ?Language
    {
        return $this->language;
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
        $manager->clear();
        // Attach entities to doctrine manager so it knows they are data in db
        if ($this->getBinding()) {
            $this->setBinding($manager->getRepository(Binding::class)->find($this->getBinding()->getId()));
        }
        if ($this->getLanguage()) {
            $this->setLanguage($manager->getRepository(Language::class)->find($this->getLanguage()->getId()));
        }
        if ($this->getAudience()) {
            $this->setAudience($manager->getRepository(Audience::class)->find($this->getAudience()->getId()));
        }
    }
}
