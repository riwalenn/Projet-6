<?php

namespace App\Entity;

use App\Repository\TrickRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrickRepository::class)
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
     */
    private $position;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $grabs;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $rotation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $flip;

    /**
     * @ORM\Column(type="string", length=255)
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

    const POSITION = [
        'Goofy' => 'goofy',
        'Regular' => 'regular'
    ];

    const GRABS = [
        'mute' => 'mute',
        'sad' => 'sad',
        'indy' => 'indy',
        'stalefish' => 'stalefish',
        'tail grab' => 'tail grab',
        'nose grab' => 'nose grab',
        'japan' => 'japan',
        'seat belt' => 'seat belt',
        'truck driver' => 'truck driver'
    ];

    const ROTATION = [
        '180' => 180,
        '360' => 360,
        '540' => 540,
        '720' => 720,
        '900' => 900,
        '1080' => 1080
    ];

    const FLIP = [
        'front flip' => 'front flip',
        'back flip' => 'back flip'
    ];

    const SLIDE = [
        'perpendiculaire' => 'perpendiculaire',
        'dans l\'axe' => 'dans l\'axe',
        'désaxé' => 'désaxé',
        'nose slide' => 'nose slide',
        'tail slide' => 'tail slide'
    ];

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

    public function getPosition($string = false): ?string
    {
        if ($string && \in_array($this->position, self::POSITION))
            return \array_search($this->position, self::POSITION);

        return $this->position;
    }

    public function setPosition(string $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getGrabs($string = false): ?string
    {
        if ($string && \in_array($this->grabs, self::GRABS))
            return \array_search($this->grabs, self::GRABS);

        return $this->grabs;
    }

    public function setGrabs(string $grabs): self
    {
        $this->grabs = $grabs;

        return $this;
    }

    public function getRotation($string = false): ?string
    {
        if ($string && \in_array($this->rotation, self::ROTATION))
            return \array_search($this->rotation, self::ROTATION);

        return $this->rotation;
    }

    public function setRotation(string $rotation): self
    {
        $this->rotation = $rotation;

        return $this;
    }

    public function getFlip($string = false): ?string
    {
        if ($string && \in_array($this->flip, self::FLIP))
            return \array_search($this->flip, self::FLIP);

        return $this->flip;
    }

    public function setFlip(string $flip): self
    {
        $this->flip = $flip;

        return $this;
    }

    public function getSlide($string = false): ?string
    {
        if ($string && \in_array($this->slide, self::SLIDE))
            return \array_search($this->slide, self::SLIDE);

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
