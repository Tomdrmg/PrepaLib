<?php

namespace App\Entity;

use App\Repository\ExerciseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExerciseRepository::class)]
class Exercise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Element $statement = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Element $solution = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?ElementList $hints = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?TagList $tags = null;

    #[ORM\ManyToOne(inversedBy: 'exercises')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ExerciseGroup $exerciseGroup = null;

    /**
     * @var Collection<int, ExercisePref>
     */
    #[ORM\OneToMany(targetEntity: ExercisePref::class, mappedBy: 'exercise')]
    private Collection $exercisePrefs;

    public function __construct()
    {
        $this->exercisePrefs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getStatement(): ?Element
    {
        return $this->statement;
    }

    public function setStatement(Element $statement): static
    {
        $this->statement = $statement;

        return $this;
    }

    public function getSolution(): ?Element
    {
        return $this->solution;
    }

    public function setSolution(Element $solution): static
    {
        $this->solution = $solution;

        return $this;
    }

    public function getHints(): ?ElementList
    {
        return $this->hints;
    }

    public function setHints(ElementList $hints): static
    {
        $this->hints = $hints;

        return $this;
    }

    public function getTags(): ?TagList
    {
        return $this->tags;
    }

    public function setTags(TagList $tags): static
    {
        $this->tags = $tags;

        return $this;
    }

    public function getExerciseGroup(): ?ExerciseGroup
    {
        return $this->exerciseGroup;
    }

    public function setExerciseGroup(?ExerciseGroup $exerciseGroup): static
    {
        $this->exerciseGroup = $exerciseGroup;

        return $this;
    }

    /**
     * @return Collection<int, ExercisePref>
     */
    public function getExercisePrefs(): Collection
    {
        return $this->exercisePrefs;
    }

    public function addExercisePref(ExercisePref $exercisePref): static
    {
        if (!$this->exercisePrefs->contains($exercisePref)) {
            $this->exercisePrefs->add($exercisePref);
            $exercisePref->setExercise($this);
        }

        return $this;
    }

    public function removeExercisePref(ExercisePref $exercisePref): static
    {
        if ($this->exercisePrefs->removeElement($exercisePref)) {
            // set the owning side to null (unless already changed)
            if ($exercisePref->getExercise() === $this) {
                $exercisePref->setExercise(null);
            }
        }

        return $this;
    }
}
