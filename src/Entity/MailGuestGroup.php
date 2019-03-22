<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MailGuestGroupRepository")
 */
class MailGuestGroup
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Mail", inversedBy="mailGuestGroups")
     * @ORM\JoinColumn(nullable=false)
     */
    private $mail;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\GuestGroup", inversedBy="mailGuestGroups")
     * @ORM\JoinColumn(nullable=false)
     */
    private $guestGroup;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMail(): ?Mail
    {
        return $this->mail;
    }

    public function setMail(?Mail $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getGuestGroup(): ?GuestGroup
    {
        return $this->guestGroup;
    }

    public function setGuestGroup(?GuestGroup $guestGroup): self
    {
        $this->guestGroup = $guestGroup;

        return $this;
    }
}
