<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WeddingRepository")
 */
class Wedding
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", mappedBy="wedding", cascade={"persist", "remove"})
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Photo", mappedBy="wedding")
     */
    private $photos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ReceptionTable", mappedBy="wedding")
     */
    private $receptionTables;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\GuestGroup", mappedBy="wedding")
     */
    private $guestGroups;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Gift", mappedBy="wedding")
     */
    private $gifts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Person", mappedBy="wedding")
     */
    private $people;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Event", mappedBy="wedding")
     */
    private $events;

    public function __construct()
    {
        $this->photos = new ArrayCollection();
        $this->receptionTables = new ArrayCollection();
        $this->guestGroups = new ArrayCollection();
        $this->gifts = new ArrayCollection();
        $this->people = new ArrayCollection();
        $this->events = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        // set (or unset) the owning side of the relation if necessary
        $newWedding = $user === null ? null : $this;
        if ($newWedding !== $user->getWedding()) {
            $user->setWedding($newWedding);
        }

        return $this;
    }

    /**
     * @return Collection|Photo[]
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(Photo $photo): self
    {
        if (!$this->photos->contains($photo)) {
            $this->photos[] = $photo;
            $photo->setWedding($this);
        }

        return $this;
    }

    public function removePhoto(Photo $photo): self
    {
        if ($this->photos->contains($photo)) {
            $this->photos->removeElement($photo);
            // set the owning side to null (unless already changed)
            if ($photo->getWedding() === $this) {
                $photo->setWedding(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ReceptionTable[]
     */
    public function getReceptionTables(): Collection
    {
        return $this->receptionTables;
    }

    public function addReceptionTable(ReceptionTable $receptionTable): self
    {
        if (!$this->receptionTables->contains($receptionTable)) {
            $this->receptionTables[] = $receptionTable;
            $receptionTable->setWedding($this);
        }

        return $this;
    }

    public function removeReceptionTable(ReceptionTable $receptionTable): self
    {
        if ($this->receptionTables->contains($receptionTable)) {
            $this->receptionTables->removeElement($receptionTable);
            // set the owning side to null (unless already changed)
            if ($receptionTable->getWedding() === $this) {
                $receptionTable->setWedding(null);
            }
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
            $guestGroup->setWedding($this);
        }

        return $this;
    }

    public function removeGuestGroup(GuestGroup $guestGroup): self
    {
        if ($this->guestGroups->contains($guestGroup)) {
            $this->guestGroups->removeElement($guestGroup);
            // set the owning side to null (unless already changed)
            if ($guestGroup->getWedding() === $this) {
                $guestGroup->setWedding(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Gift[]
     */
    public function getGifts(): Collection
    {
        return $this->gifts;
    }

    public function addGift(Gift $gift): self
    {
        if (!$this->gifts->contains($gift)) {
            $this->gifts[] = $gift;
            $gift->setWedding($this);
        }

        return $this;
    }

    public function removeGift(Gift $gift): self
    {
        if ($this->gifts->contains($gift)) {
            $this->gifts->removeElement($gift);
            // set the owning side to null (unless already changed)
            if ($gift->getWedding() === $this) {
                $gift->setWedding(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Person[]
     */
    public function getPeople(): Collection
    {
        return $this->people;
    }

    public function addPerson(Person $person): self
    {
        if (!$this->people->contains($person)) {
            $this->people[] = $person;
            $person->setWedding($this);
        }

        return $this;
    }

    public function removePerson(Person $person): self
    {
        if ($this->people->contains($person)) {
            $this->people->removeElement($person);
            // set the owning side to null (unless already changed)
            if ($person->getWedding() === $this) {
                $person->setWedding(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Event[]
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->setWedding($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->contains($event)) {
            $this->events->removeElement($event);
            // set the owning side to null (unless already changed)
            if ($event->getWedding() === $this) {
                $event->setWedding(null);
            }
        }

        return $this;
    }
}
