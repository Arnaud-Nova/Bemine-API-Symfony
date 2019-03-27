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
     * @ORM\ManyToOne(targetEntity="App\Entity\Wedding", inversedBy="events")
     */
    private $wedding;

    // /**
    //  * @ORM\ManyToOne(targetEntity="App\Entity\Event", inversedBy="events")
    //  */
    // private $event;

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
     * @ORM\ManyToMany(targetEntity="App\Entity\GuestGroup", mappedBy="event")
     */
    private $guestGroups;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $active;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $map;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $name;

    public function __construct()
    {
        $this->guestGroups = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWedding(): ?Wedding
    {
        return $this->wedding;
    }

    public function setWedding(?Wedding $wedding): self
    {
        $this->wedding = $wedding;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPostcode(): ?int
    {
        return $this->postcode;
    }

    public function setPostcode(int $postcode): self
    {
        $this->postcode = $postcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
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

    public function getActive(): ?bool
    {
        return $this->isActive;
    }

    public function setActive(?bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getMap(): ?string
    {
        return $this->map;
    }

    public function setMap(?string $map): self
    {
        $this->map = $map;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
