<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PersonRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Person
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $firstname;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $attendance;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $newlyweds;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $menu;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $allergies = 0;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $halal = 0;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $noAlcohol = 0;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $vegetarian = 0;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $vegan = 0;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $casher = 0;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $commentAllergies;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $seatNumber;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\GuestGroup", inversedBy="people", cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $guestGroup;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ReceptionTable", inversedBy="people")
     */
    private $receptionTable;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\GuestGroup", mappedBy="contactPerson", cascade={"persist", "remove"})
     */
    private $contactGuestGroup;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Wedding", inversedBy="people")
     * @ORM\JoinColumn(nullable=false)
     */
    private $wedding;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getAttendance(): ?bool
    {
        return $this->attendance;
    }

    public function setAttendance(?bool $attendance): self
    {
        $this->attendance = $attendance;

        return $this;
    }

    public function getNewlyweds(): ?bool
    {
        return $this->newlyweds;
    }

    public function setNewlyweds(bool $newlyweds): self
    {
        $this->newlyweds = $newlyweds;

        return $this;
    }

    public function getMenu(): ?string
    {
        return $this->menu;
    }

    public function setMenu(?string $menu): self
    {
        $this->menu = $menu;

        return $this;
    }

    public function getAllergies(): ?bool
    {
        return $this->allergies;
    }

    public function setAllergies(bool $allergies): self
    {
        $this->allergies = $allergies;

        return $this;
    }

    public function getHalal(): ?bool
    {
        return $this->halal;
    }

    public function setHalal(bool $halal): self
    {
        $this->halal = $halal;

        return $this;
    }

    public function getNoAlcohol(): ?bool
    {
        return $this->noAlcohol;
    }

    public function setNoAlcohol(bool $noAlcohol): self
    {
        $this->noAlcohol = $noAlcohol;

        return $this;
    }

    public function getVegetarian(): ?bool
    {
        return $this->vegetarian;
    }

    public function setVegetarian(bool $vegetarian): self
    {
        $this->vegetarian = $vegetarian;

        return $this;
    }

    public function getVegan(): ?bool
    {
        return $this->vegan;
    }

    public function setVegan(bool $vegan): self
    {
        $this->vegan = $vegan;

        return $this;
    }

    public function getCasher(): ?bool
    {
        return $this->casher;
    }

    public function setCasher(bool $casher): self
    {
        $this->casher = $casher;

        return $this;
    }

    public function getCommentAllergies(): ?string
    {
        return $this->commentAllergies;
    }

    public function setCommentAllergies(?string $commentAllergies): self
    {
        $this->commentAllergies = $commentAllergies;

        return $this;
    }

    public function getSeatNumber(): ?int
    {
        return $this->seatNumber;
    }

    public function setSeatNumber(?int $seatNumber): self
    {
        $this->seatNumber = $seatNumber;

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

    public function getReceptionTable(): ?ReceptionTable
    {
        return $this->receptionTable;
    }

    public function setReceptionTable(?ReceptionTable $receptionTable): self
    {
        $this->receptionTable = $receptionTable;

        return $this;
    }

    public function getContactGuestGroup(): ?GuestGroup
    {
        return $this->contactGuestGroup;
    }

    public function setContactGuestGroup(GuestGroup $contactGuestGroup): self
    {
        $this->contactGuestGroup = $contactGuestGroup;

        // set the owning side of the relation if necessary
        if ($this !== $contactGuestGroup->getContactPerson()) {
            $contactGuestGroup->setContactPerson($this);
        }

        return $this;
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

}
