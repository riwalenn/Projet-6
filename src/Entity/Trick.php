<?php

namespace App\Entity;

use App\Repository\TrickRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TrickRepository::class)
 * @UniqueEntity("title")
 */
class Trick
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=3, max=255, minMessage="Vous devez entrer au moins 3 caractères", maxMessage="Vous ne pouvez pas entrer plus de 255 caractères")
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $User;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=3, max=20, minMessage="Vous devez entrer au moins 3 caractères", maxMessage="Vous devez entrer des mots clés, pas plus de 20 caractères")
     */
    private $position;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=3, max=20, minMessage="Vous devez entrer au moins 3 caractères", maxMessage="Vous devez entrer des mots clés, pas plus de 20 caractères")
     */
    private $grabs;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(max="1500", maxMessage="Vous devez entrer des valeurs entre 0 et 1600")
     */
    private $rotation;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=3, max=20, minMessage="Vous devez entrer au moins 3 caractères", maxMessage="Vous devez entrer des mots clés, pas plus de 20 caractères")
     */
    private $flip;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=3, max=20, minMessage="Vous devez entrer au moins 3 caractères", maxMessage="Vous devez entrer des mots clés, pas plus de 20 caractères")
     */
    private $slide;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="Trick")
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity=TrickLibrary::class, mappedBy="trick")
     */
    private $trickLibraries;

    /**
     * @ORM\OneToMany(targetEntity=TrickHistory::class, mappedBy="trick")
     */
    private $trickHistories;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->trickLibraries = new ArrayCollection();
        $this->trickHistories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): self
    {
        $this->User = $User;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getGrabs(): ?string
    {
        return $this->grabs;
    }

    public function setGrabs(string $grabs): self
    {
        $this->grabs = $grabs;

        return $this;
    }

    public function getRotation(): ?string
    {
        return $this->rotation;
    }

    public function setRotation(string $rotation): self
    {
        $this->rotation = $rotation;

        return $this;
    }

    public function getFlip(): ?string
    {
        return $this->flip;
    }

    public function setFlip(string $flip): self
    {
        $this->flip = $flip;

        return $this;
    }

    public function getSlide(): ?string
    {
        return $this->slide;
    }

    public function setSlide(string $slide): self
    {
        $this->slide = $slide;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setTrick($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getTrick() === $this) {
                $comment->setTrick(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TrickLibrary[]
     */
    public function getTrickLibraries(): Collection
    {
        return $this->trickLibraries;
    }

    public function addTrickLibrary(TrickLibrary $trickLibrary): self
    {
        if (!$this->trickLibraries->contains($trickLibrary)) {
            $this->trickLibraries[] = $trickLibrary;
            $trickLibrary->setTrick($this);
        }

        return $this;
    }

    public function removeTrickLibrary(TrickLibrary $trickLibrary): self
    {
        if ($this->trickLibraries->contains($trickLibrary)) {
            $this->trickLibraries->removeElement($trickLibrary);
            // set the owning side to null (unless already changed)
            if ($trickLibrary->getTrick() === $this) {
                $trickLibrary->setTrick(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TrickHistory[]
     */
    public function getTrickHistories(): Collection
    {
        return $this->trickHistories;
    }

    public function addTrickHistory(TrickHistory $trickHistory): self
    {
        if (!$this->trickHistories->contains($trickHistory)) {
            $this->trickHistories[] = $trickHistory;
            $trickHistory->setTrick($this);
        }

        return $this;
    }

    public function removeTrickHistory(TrickHistory $trickHistory): self
    {
        if ($this->trickHistories->contains($trickHistory)) {
            $this->trickHistories->removeElement($trickHistory);
            // set the owning side to null (unless already changed)
            if ($trickHistory->getTrick() === $this) {
                $trickHistory->setTrick(null);
            }
        }

        return $this;
    }
}
