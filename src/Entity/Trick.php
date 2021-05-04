<?php

namespace App\Entity;

use App\Framework\Constantes;
use App\Repository\TrickRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TrickRepository::class)
 * @UniqueEntity(
 *     fields={"title"},
 *     message="Le titre {{ value }} est déjà utilisé !"
 * )
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
     */
    private $slug;

    /**
     * @ORM\Column(type="string")
     * @Assert\Length(
     *     min = 25,
     *     max = 255,
     *     minMessage="Votre titre doit comporter un minimum de {{ limit }} caractères",
     *     maxMessage="Votre titre doit comporter au maximum {{ limit }} caractères"
     * )
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $User;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Choice(choices=Constantes::POSITION, message="Merci de choisir une position dans la liste")
     * @Assert\NotBlank()
     */
    private $position;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Choice(choices=Constantes::GRABS, message="Merci de choisir un grab dans la liste")
     * @Assert\NotBlank()
     */
    private $grabs;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $rotation;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Choice(choices=Constantes::FLIP, message="Merci de choisir un flip dans la liste")
     * @Assert\NotBlank()
     */
    private $flip;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Choice(choices=Constantes::SLIDE, message="Merci de choisir un slide dans la liste")
     * @Assert\NotBlank()
     */
    private $slide;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="Trick", cascade={"persist"})
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity=TrickLibrary::class, mappedBy="trick", cascade={"persist"})
     */
    private $trickLibraries;

    /**
     * @ORM\OneToMany(targetEntity=TrickHistory::class, mappedBy="trick", cascade={"persist"})
     */
    private $trickHistories;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->trickLibraries = new ArrayCollection();
        $this->trickHistories = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->slug;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPosition($string = false): ?string
    {
        if ($string && \in_array($this->position, Constantes::POSITION))
            return \array_search($this->position, Constantes::POSITION);

        return $this->position;
    }

    public function setPosition(string $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getGrabs($string = false): ?string
    {
        if ($string && \in_array($this->grabs, Constantes::GRABS))
            return \array_search($this->grabs, Constantes::GRABS);

        return $this->grabs;
    }

    public function setGrabs(string $grabs): self
    {
        $this->grabs = $grabs;

        return $this;
    }

    public function getRotation($string = false): ?string
    {
        if ($string && \in_array($this->rotation, Constantes::ROTATION))
            return \array_search($this->rotation, Constantes::ROTATION);

        return $this->rotation;
    }

    public function setRotation(string $rotation): self
    {
        $this->rotation = $rotation;

        return $this;
    }

    public function getFlip($string = false): ?string
    {
        if ($string && \in_array($this->flip, Constantes::FLIP))
            return \array_search($this->flip, Constantes::FLIP);

        return $this->flip;
    }

    public function setFlip(string $flip): self
    {
        $this->flip = $flip;

        return $this;
    }

    public function getSlide($string = false): ?string
    {
        if ($string && \in_array($this->slide, Constantes::SLIDE))
            return \array_search($this->slide, Constantes::SLIDE);

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
