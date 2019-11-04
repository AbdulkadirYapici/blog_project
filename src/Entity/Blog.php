<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BlogRepository")
 */
class Blog
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
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $preview_img;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $summary;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;



    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Category", inversedBy="blog_id")
     */
    private $category_id;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Tag", inversedBy="blog_id")
     */
    private $tag_id;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="blog_id")
     */
    private $user_id;



    public function __construct()
    {
        $this->category_id = new ArrayCollection();
        $this->tag_id = new ArrayCollection();
        $this->user_id = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPreviewImg(): ?string
    {
        return $this->preview_img;
    }

    public function setPreviewImg(?string $preview_img): self
    {
        $this->preview_img = $preview_img;

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }






    /**
     * @return Collection|Category[]
     */
    public function getCategoryId(): Collection
    {
        return $this->category_id;
    }

    public function addCategoryId(Category $categoryId): self
    {
        if (!$this->category_id->contains($categoryId)) {
            $this->category_id[] = $categoryId;
        }

        return $this;
    }

    public function removeCategoryId(Category $categoryId): self
    {
        if ($this->category_id->contains($categoryId)) {
            $this->category_id->removeElement($categoryId);
        }

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTagId(): Collection
    {
        return $this->tag_id;
    }

    public function addTagId(Tag $tagId): self
    {
        if (!$this->tag_id->contains($tagId)) {
            $this->tag_id[] = $tagId;
        }

        return $this;
    }

    public function removeTagId(Tag $tagId): self
    {
        if ($this->tag_id->contains($tagId)) {
            $this->tag_id->removeElement($tagId);
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUserId(): Collection
    {
        return $this->user_id;
    }

    public function addUserId(User $userId): self
    {
        if (!$this->user_id->contains($userId)) {
            $this->user_id[] = $userId;
            $userId->addBlogId($this);
        }

        return $this;
    }

    public function removeUserId(User $userId): self
    {
        if ($this->user_id->contains($userId)) {
            $this->user_id->removeElement($userId);
            $userId->removeBlogId($this);
        }

        return $this;
    }




}
