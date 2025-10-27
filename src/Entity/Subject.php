<?php

namespace App\Entity;

use App\Repository\SubjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubjectRepository::class)]
class Subject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $faicon = null;

    /**
     * @var Collection<int, ExerciseCategory>
     */
    #[ORM\OneToMany(targetEntity: ExerciseCategory::class, mappedBy: 'subject')]
    private Collection $exerciseCategories;

    /**
     * @var Collection<int, RevisionSheet>
     */
    #[ORM\OneToMany(targetEntity: RevisionSheet::class, mappedBy: 'subject')]
    private Collection $revisionSheets;

    public function __construct()
    {
        $this->exerciseCategories = new ArrayCollection();
        $this->revisionSheets = new ArrayCollection();
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

    public function getFaicon(): ?string
    {
        return $this->faicon;
    }

    public function setFaicon(string $faicon): static
    {
        $this->faicon = $faicon;

        return $this;
    }

    public function getHighestExerciseCategories(): Collection
    {
        return $this->exerciseCategories->filter(function ($category) {
            return $category->getParent() === null;
        });
    }

    /**
     * @return Collection<int, ExerciseCategory>
     */
    public function getExerciseCategories(): Collection
    {
        return $this->exerciseCategories;
    }

    public function addExerciseCategory(ExerciseCategory $title): static
    {
        if (!$this->exerciseCategories->contains($title)) {
            $this->exerciseCategories->add($title);
            $title->setSubject($this);
        }

        return $this;
    }

    public function removeExerciseCategory(ExerciseCategory $title): static
    {
        if ($this->exerciseCategories->removeElement($title)) {
            // set the owning side to null (unless already changed)
            if ($title->getSubject() === $this) {
                $title->setSubject(null);
            }
        }

        return $this;
    }

    public function countExercises(): int
    {
        $total = 0;
        foreach ($this->exerciseCategories as $exerciseCategory) {
            $total += $exerciseCategory->getExercises()->count();
        }

        return $total;
    }

    public function getHighestRevisionSheets(): Collection
    {
        return $this->revisionSheets->filter(function ($sheet) {
            return $sheet->getParent() === null;
        });
    }

    public function countRevisionSheets(): int
    {
        return $this->getHighestRevisionSheets()->count();
    }

    public function countQuestions(): int
    {
        $total = 0;
        foreach ($this->revisionSheets as $sheet) {
            $total += $sheet->countQuestions();
        }

        return $total;
    }

    public function getExercises(): Collection
    {
        $allExercises = new ArrayCollection();

        foreach ($this->exerciseCategories as $category) {
            while ($category->getChildren()->first()) {
                $category = $category->getChildren()->first();
            }

            $allExercises->add($category->getExercises()->first());
        }

        return $allExercises;
    }

    /**
     * @return Collection<int, RevisionSheet>
     */
    public function getRevisionSheets(): Collection
    {
        return $this->revisionSheets;
    }

    public function addRevisionSheet(RevisionSheet $revisionSheet): static
    {
        if (!$this->revisionSheets->contains($revisionSheet)) {
            $this->revisionSheets->add($revisionSheet);
            $revisionSheet->setSubject($this);
        }

        return $this;
    }

    public function removeRevisionSheet(RevisionSheet $revisionSheet): static
    {
        if ($this->revisionSheets->removeElement($revisionSheet)) {
            // set the owning side to null (unless already changed)
            if ($revisionSheet->getSubject() === $this) {
                $revisionSheet->setSubject(null);
            }
        }

        return $this;
    }
}
