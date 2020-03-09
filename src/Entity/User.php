<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use function foo\func;

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

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserNotification", mappedBy="user", orphanRemoval=true)
     */
    private $notifications;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserSettings", mappedBy="user", orphanRemoval=true)
     */
    private $settings;

    public function __construct()
    {
        $this->useArticles = new ArrayCollection();
        $this->useFollowedSections = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->settings = new ArrayCollection();
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

    /**
     * @return Collection|UserNotification[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function getUnreadNotifications()
    {
        $unreadNotifications = [];
        foreach ($this->notifications as $notification)
        {
            if(!$notification->getHasBeenRead())
            {
                $unreadNotifications[] = $notification;
            }
        }
        return $unreadNotifications;
    }

    public function addNotification(UserNotification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setUser($this);
        }

        return $this;
    }

    public function removeNotification(UserNotification $notification): self
    {
        if ($this->notifications->contains($notification)) {
            $this->notifications->removeElement($notification);
            // set the owning side to null (unless already changed)
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserSettings[]
     */
    public function getSettings(): Collection
    {
        return $this->settings;
    }

    public function addSetting(UserSettings $setting): self
    {
        if (!$this->settings->contains($setting)) {
            $this->settings[] = $setting;
            $setting->setUserId($this);
        }

        return $this;
    }

    public function removeSetting(UserSettings $setting): self
    {
        if ($this->settings->contains($setting)) {
            $this->settings->removeElement($setting);
            // set the owning side to null (unless already changed)
            if ($setting->getUserId() === $this) {
                $setting->setUserId(null);
            }
        }

        return $this;
    }
}
