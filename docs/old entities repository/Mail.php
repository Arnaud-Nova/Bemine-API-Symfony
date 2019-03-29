<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MailRepository")
 */
class Mail
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MailGuestGroup", mappedBy="mail")
     */
    private $mailGuestGroups;

    public function __construct()
    {
        $this->mailGuestGroups = new ArrayCollection();
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Collection|MailGuestGroup[]
     */
    public function getMailGuestGroups(): Collection
    {
        return $this->mailGuestGroups;
    }

    public function addMailGuestGroup(MailGuestGroup $mailGuestGroup): self
    {
        if (!$this->mailGuestGroups->contains($mailGuestGroup)) {
            $this->mailGuestGroups[] = $mailGuestGroup;
            $mailGuestGroup->setMail($this);
        }

        return $this;
    }

    public function removeMailGuestGroup(MailGuestGroup $mailGuestGroup): self
    {
        if ($this->mailGuestGroups->contains($mailGuestGroup)) {
            $this->mailGuestGroups->removeElement($mailGuestGroup);
            // set the owning side to null (unless already changed)
            if ($mailGuestGroup->getMail() === $this) {
                $mailGuestGroup->setMail(null);
            }
        }

        return $this;
    }
}
