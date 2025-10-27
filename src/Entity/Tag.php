<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 7)]
    private ?string $color = null;

    /**
     * @var Collection<int, Exercise>
     */
    #[ORM\ManyToMany(targetEntity: Exercise::class, mappedBy: 'tags')]
    private Collection $exercises;

    /**
     * @var Collection<int, ExerciseCategory>
     */
    #[ORM\ManyToMany(targetEntity: ExerciseCategory::class, mappedBy: 'tags')]
    private Collection $exerciseCategories;

    #[ORM\ManyToOne(inversedBy: 'tags')]
    private ?RevisionSheet $revisionSheet = null;

    public function __construct()
    {
        $this->courseElements = new ArrayCollection();
        $this->exercises = new ArrayCollection();
        $this->exerciseCategories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return Collection<int, Exercise>
     */
    public function getExercises(): Collection
    {
        return $this->exercises;
    }

    public function addExercise(Exercise $exercise): static
    {
        if (!$this->exercises->contains($exercise)) {
            $this->exercises->add($exercise);
            $exercise->addTag($this);
        }

        return $this;
    }

    public function removeExercise(Exercise $exercise): static
    {
        if ($this->exercises->removeElement($exercise)) {
            $exercise->removeTag($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, ExerciseCategory>
     */
    public function getExerciseCategories(): Collection
    {
        return $this->exerciseCategories;
    }

    public function addExerciseCategory(ExerciseCategory $exerciseCategory): static
    {
        if (!$this->exerciseCategories->contains($exerciseCategory)) {
            $this->exerciseCategories->add($exerciseCategory);
            $exerciseCategory->addTag($this);
        }

        return $this;
    }

    public function removeExerciseCategory(ExerciseCategory $exerciseCategory): static
    {
        if ($this->exerciseCategories->removeElement($exerciseCategory)) {
            $exerciseCategory->removeTag($this);
        }

        return $this;
    }

    public function getRevisionSheet(): ?RevisionSheet
    {
        return $this->revisionSheet;
    }

    public function setRevisionSheet(?RevisionSheet $revisionSheet): static
    {
        $this->revisionSheet = $revisionSheet;

        return $this;
    }
}
