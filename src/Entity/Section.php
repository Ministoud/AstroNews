<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SectionRepository")
 */
class Section
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("get:accepted")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("get:accepted")
     */
    private $secName;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="useFollowedSections")
     */
    private $secUsers;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Article", mappedBy="artSections")
     */
    private $secArticles;

    public function __construct()
    {
        $this->secUsers = new ArrayCollection();
        $this->secArticles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSecName(): ?string
    {
        return $this->secName;
    }

    public function setSecName(string $secName): self
    {
        $this->secName = $secName;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getSecUsers(): Collection
    {
        return $this->secUsers;
    }

    public function addSecUser(User $secUser): self
    {
        if (!$this->secUsers->contains($secUser)) {
            $this->secUsers[] = $secUser;
            $secUser->addUseFollowedSection($this);
        }

        return $this;
    }

    public function removeSecUser(User $secUser): self
    {
        if ($this->secUsers->contains($secUser)) {
            $this->secUsers->removeElement($secUser);
            $secUser->removeUseFollowedSection($this);
        }

        return $this;
    }

    /**
     * @return Collection|Article[]
     */
    public function getSecArticles(): Collection
    {
        return $this->secArticles;
    }

    public function addSecArticle(Article $secArticle): self
    {
        if (!$this->secArticles->contains($secArticle)) {
            $this->secArticles[] = $secArticle;
            $secArticle->addArtSection($this);
        }

        return $this;
    }

    public function removeSecArticle(Article $secArticle): self
    {
        if ($this->secArticles->contains($secArticle)) {
            $this->secArticles->removeElement($secArticle);
            $secArticle->removeArtSection($this);
        }

        return $this;
    }
}
