<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserNotificationSettingsRepository")
 */
class UserNotificationSettings
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="settings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $chanDatabase;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $chanEmail;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $chanSms;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $chanBrowser;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $chanChat;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
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

    public function getChanDatabase(): ?bool
    {
        return $this->chanDatabase;
    }

    public function setChanDatabase(?bool $chanDatabase): self
    {
        $this->chanDatabase = $chanDatabase;

        return $this;
    }

    public function getChanEmail(): ?bool
    {
        return $this->chanEmail;
    }

    public function setChanEmail(?bool $chanEmail): self
    {
        $this->chanEmail = $chanEmail;

        return $this;
    }

    public function getChanSms(): ?bool
    {
        return $this->chanSms;
    }

    public function setChanSms(?bool $chanSms): self
    {
        $this->chanSms = $chanSms;

        return $this;
    }

    public function getChanBrowser(): ?bool
    {
        return $this->chanBrowser;
    }

    public function setChanBrowser(?bool $chanBrowser): self
    {
        $this->chanBrowser = $chanBrowser;

        return $this;
    }

    public function getChanChat(): ?bool
    {
        return $this->chanChat;
    }

    public function setChanChat(?bool $chanChat): self
    {
        $this->chanChat = $chanChat;

        return $this;
    }
}
