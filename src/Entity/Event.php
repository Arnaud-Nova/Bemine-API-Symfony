<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 */
class Event
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $postcode;

    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $schedule;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $informations;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Wedding", inversedBy="events")
     */
    private $weddings;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\GuestGroup", mappedBy="event")
     */
    private $guestGroups;

    public function __construct()
    {
        $this->weddings = new ArrayCollection();
        $this->guestGroups = new ArrayCollection();
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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPostcode(): ?int
    {
        return $this->postcode;
    }

    public function setPostcode(?int $postcode): self
    {
        $this->postcode = $postcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getSchedule(): ?\DateTimeInterface
    {
        return $this->schedule;
    }

    public function setSchedule(?\DateTimeInterface $schedule): self
    {
        $this->schedule = $schedule;

        return $this;
    }

    public function getInformations(): ?string
    {
        return $this->informations;
    }

    public function setInformations(?string $informations): self
    {
        $this->informations = $informations;

        return $this;
    }

    /**
     * @return Collection|Wedding[]
     */
    public function getWeddings(): Collection
    {
        return $this->weddings;
    }

    public function addWedding(Wedding $wedding): self
    {
        if (!$this->weddings->contains($wedding)) {
            $this->weddings[] = $wedding;
        }

        return $this;
    }

    public function removeWedding(Wedding $wedding): self
    {
        if ($this->weddings->contains($wedding)) {
            $this->weddings->removeElement($wedding);
        }

        return $this;
    }

    /**
     * @return Collection|GuestGroup[]
     */
    public function getGuestGroups(): Collection
    {
        return $this->guestGroups;
    }

    public function addGuestGroup(GuestGroup $guestGroup): self
    {
        if (!$this->guestGroups->contains($guestGroup)) {
            $this->guestGroups[] = $guestGroup;
            $guestGroup->addEvent($this);
        }

        return $this;
    }

    public function removeGuestGroup(GuestGroup $guestGroup): self
    {
        if ($this->guestGroups->contains($guestGroup)) {
            $this->guestGroups->removeElement($guestGroup);
            $guestGroup->removeEvent($this);
        }

        return $this;
    }
}
