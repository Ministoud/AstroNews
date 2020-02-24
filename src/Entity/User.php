<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 *  fields="useEmail",
 *  message="Cet email est déjà utilisé !"
 * )
 */
class User implements UserInterface
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
    private $useFirstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("get:accepted")
     */
    private $useLastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(
     *      message = "Votre email n'est pas correcte."
     * )
     * @Groups("get:accepted")
     */
    private $useEmail;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $usePassword;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Article", mappedBy="artAuthor")
     */
    private $useArticles;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Section", inversedBy="secUsers")
     */
    private $useFollowedSections;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $Roles = [];

    public function __construct()
    {
        $this->useArticles = new ArrayCollection();
        $this->useFollowedSections = new ArrayCollection();
    }

    public function setId($id)
    {
        $this->id = $id;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUseFirstName(): ?string
    {
        return $this->useFirstName;
    }

    public function setUseFirstName(string $useFirstName): self
    {
        $this->useFirstName = $useFirstName;

        return $this;
    }

    public function getUseLastName(): ?string
    {
        return $this->useLastName;
    }

    public function setUseLastName(string $useLastName): self
    {
        $this->useLastName = $useLastName;

        return $this;
    }

    public function getUseEmail(): ?string
    {
        return $this->useEmail;
    }

    public function setUseEmail(string $useEmail): self
    {
        $this->useEmail = $useEmail;

        return $this;
    }

    public function getUsePassword(): ?string
    {
        return $this->usePassword;
    }

    public function setUsePassword(string $usePassword): self
    {
        $this->usePassword = $usePassword;

        return $this;
    }

    /**
     * @return Collection|Article[]
     */
    public function getUseArticles(): Collection
    {
        return $this->useArticles;
    }

    public function addUseArticle(Article $useArticle): self
    {
        if (!$this->useArticles->contains($useArticle)) {
            $this->useArticles[] = $useArticle;
            $useArticle->setArtAuthor($this);
        }

        return $this;
    }

    public function removeUseArticle(Article $useArticle): self
    {
        if ($this->useArticles->contains($useArticle)) {
            $this->useArticles->removeElement($useArticle);
            // set the owning side to null (unless already changed)
            if ($useArticle->getArtAuthor() === $this) {
                $useArticle->setArtAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Section[]
     */
    public function getUseFollowedSections(): Collection
    {
        return $this->useFollowedSections;
    }

    public function addUseFollowedSection(Section $useFollowedSection): self
    {
        if (!$this->useFollowedSections->contains($useFollowedSection)) {
            $this->useFollowedSections[] = $useFollowedSection;
        }

        return $this;
    }

    public function removeUseFollowedSection(Section $useFollowedSection): self
    {
        if ($this->useFollowedSections->contains($useFollowedSection)) {
            $this->useFollowedSections->removeElement($useFollowedSection);
        }

        return $this;
    }

    public function getPassword()
    {
        return $this->usePassword;
    }

    public function getSalt()
    {
    }

    public function getUsername()
    {
        return $this->getUseFirstName() . " " . $this->getUseLastName();
    }

    public function eraseCredentials()
    {
        
    }

    public function getRoles(): ?array
    {
        $roles = $this->Roles;
        $roles[] = 'ROLE_USER';

        return \array_unique($roles);
    }

    public function setRoles(?array $Roles): self
    {
        $this->Roles = $Roles;

        return $this;
    }

}
