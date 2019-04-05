<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=191)
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=191, nullable=true)
     */
    private $urlAvatar;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Wedding", inversedBy="user", cascade={"persist", "remove"})
     */
    private $wedding;

    /**
    * @ORM\Column(type="string", length=191, unique=true, nullable=true)
    */
    private $apiToken;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles()
    {
        $roles[] = $this->roles;

        return $roles;
    }

    public function setRoles($roles): self
    {
        $this->roles = $roles;

        return $this;
    }
        
    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUrlAvatar(): ?string
    {
        return $this->urlAvatar;
    }

    public function setUrlAvatar(?string $urlAvatar): self
    {
        $this->urlAvatar = $urlAvatar;

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

    /**

     * Get the value of apiToken
     */ 
    public function getApiToken()
    {
        return $this->apiToken;
    }

    /**
     * Set the value of apiToken
     *
     * @return self
     */ 
    public function setApiToken($apiToken)
    {
        $this->apiToken = $apiToken;

        return $this;
    }
    
    /**
    * @ORM\PrePersist 
    * @ORM\PreUpdate
    */
    public function defaultValues()
    {
        if (empty($this->roles)) {
             $roles = '{"name": "Couple", "code": "ROLE_COUPLE"}';
             $this->roles = $roles;
        }

        if (!isset($this->isActive)) {
            $this->isActive = true;
        }

    }
}
