<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Blog", mappedBy="category_id")
     */
    private $blog_id;

    public function __construct()
    {
        $this->blog_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Blog[]
     */
    public function getBlogId(): Collection
    {
        return $this->blog_id;
    }

    /**
     * @param ArrayCollection $blog_id
     */
    public function setBlogId(ArrayCollection $blog_id): void
    {
        $this->blog_id = $blog_id;
    }



    public function addBlogId(Blog $blogId): self
    {
        if (!$this->blog_id->contains($blogId)) {
            $this->blog_id[] = $blogId;
            $blogId->addCategoryId($this);
        }

        return $this;
    }

    public function removeBlogId(Blog $blogId): self
    {
        if ($this->blog_id->contains($blogId)) {
            $this->blog_id->removeElement($blogId);
            $blogId->removeCategoryId($this);
        }

        return $this;
    }
    public function __toString()
    {
        return sprintf("%s ", $this->name);
    }
}
