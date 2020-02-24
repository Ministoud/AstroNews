<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 */
class Article
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
    private $artName;

    /**
     * @ORM\Column(type="text")
     * @Groups("get:accepted")
     */
    private $artContent;

    /**
     * @ORM\Column(type="date")
     * @Groups("get:accepted")
     */
    private $artCreationDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups("get:accepted")
     */
    private $artEditionDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="useArticles")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("get:accepted")
     */
    private $artAuthor;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Section", inversedBy="secArticles")
     * @Groups("get:accepted")
     */
    private $artSections;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Image()
     * @Groups("get:accepted")
     */
    private $artImage;

    public function __construct()
    {
        $this->artSections = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArtName(): ?string
    {
        return $this->artName;
    }

    public function setArtName(string $artName): self
    {
        $this->artName = $artName;

        return $this;
    }

    public function getArtContent(): ?string
    {
        return $this->artContent;
    }

    public function setArtContent(string $artContent): self
    {
        $this->artContent = $artContent;

        return $this;
    }

    public function getArtCreationDate(): ?\DateTimeInterface
    {
        return $this->artCreationDate;
    }

    public function setArtCreationDate(\DateTimeInterface $artCreationDate): self
    {
        $this->artCreationDate = $artCreationDate;

        return $this;
    }

    public function getArtEditionDate(): ?\DateTimeInterface
    {
        return $this->artEditionDate;
    }

    public function setArtEditionDate(?\DateTimeInterface $artEditionDate): self
    {
        $this->artEditionDate = $artEditionDate;

        return $this;
    }

    public function getArtAuthor(): ?User
    {
        return $this->artAuthor;
    }

    public function setArtAuthor(?User $artAuthor): self
    {
        $this->artAuthor = $artAuthor;

        return $this;
    }

    /**
     * @return Collection|Section[]
     */
    public function getArtSections(): Collection
    {
        return $this->artSections;
    }

    public function addArtSection(Section $artSection): self
    {
        if (!$this->artSections->contains($artSection)) {
            $this->artSections[] = $artSection;
        }

        return $this;
    }

    public function removeArtSection(Section $artSection): self
    {
        if ($this->artSections->contains($artSection)) {
            $this->artSections->removeElement($artSection);
        }

        return $this;
    }

    public function getArtImage(): ?string
    {
        return $this->artImage;
    }

    public function setArtImage(?string $artImage): self
    {
        $this->artImage = $artImage;

        return $this;
    }
}
