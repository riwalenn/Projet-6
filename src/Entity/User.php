<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 *     fields={"email"},
 *     message="L'email {{ value }} est déjà utilisée !"
 * )
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private ?string $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private ?string $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="8", minMessage="Votre mot de passe doit faire {{ limit }} caractères minimum.")
     */
    private ?string $password;

    /**
     * @Assert\EqualTo(propertyPath="password", message="Vous n'avez pas tapé le même mot de passe !")
     */
    public $confirm_password;

    /**
     * @ORM\Column(type="integer", length=255)
     */
    private ?int $image;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     */
    private ?string $token;

    /**
     * @ORM\OneToMany(targetEntity=Trick::class, mappedBy="User")
     */
    private ArrayCollection $users;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private DateTime $created_at;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $is_active;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="User")
     */
    private Comment $comment;

    /**
     * @ORM\OneToMany(targetEntity=TrickHistory::class, mappedBy="user")
     */
    private TrickHistory $trickHistories;

    /**
     * @ORM\Column(type="array")
     */
    private array $roles = [];

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getImage(): ?int
    {
        return $this->image;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    /**
     * @ORM\PrePersist()
     * @param \DateTimeInterface $created_at
     * @return $this
     */
    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->is_active = $isActive;
        return $this;
    }

    /**
     * @return Collection|Trick[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUsers(Trick $users): self
    {
        if (!$this->users->contains($users)) {
            $this->users[] = $users;
            $users->setUser($this);
        }

        return $this;
    }

    public function removeUsers(Trick $users): self
    {
        if ($this->users->contains($users)) {
            $this->$users->removeElement($users);
            // set the owning side to null (unless already changed)
            if ($users->getUser() === $this) {
                $users->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Comment
     */
    public function getComments(): Comment
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comment->contains($comment)) {
            $this->comment[] = $comment;
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comment->contains($comment)) {
            $this->comment->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return TrickHistory
     */
    public function getTrickHistories(): TrickHistory
    {
        return $this->trickHistories;
    }

    public function addTrickHistory(TrickHistory $trickHistory): self
    {
        if (!$this->trickHistories->contains($trickHistory)) {
            $this->trickHistories[] = $trickHistory;
            $trickHistory->setUser($this);
        }

        return $this;
    }

    public function removeTrickHistory(TrickHistory $trickHistory): self
    {
        if ($this->trickHistories->contains($trickHistory)) {
            $this->trickHistories->removeElement($trickHistory);
            // set the owning side to null (unless already changed)
            if ($trickHistory->getUser() === $this) {
                $trickHistory->setUser(null);
            }
        }

        return $this;
    }

    public function getSalt()
    {
        return null;
    }

    public function getRoles(): ?array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials()
    {
    }

    /**
     * Transform to string
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getUsername();
    }
}
