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

    /**
     * @var Collection<int, Course>
     */
    #[ORM\OneToMany(targetEntity: Course::class, mappedBy: 'subject')]
    private Collection $courses;

    #[ORM\Column(length: 255)]
    private ?string $faicon = null;

    /**
     * @var Collection<int, ExerciseCategory>
     */
    #[ORM\OneToMany(targetEntity: ExerciseCategory::class, mappedBy: 'subject')]
    private Collection $exerciseCategories;

    public function __construct()
    {
        $this->courses = new ArrayCollection();
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

    /**
     * @return Collection<int, Course>
     */
    public function getCourses(): Collection
    {
        return $this->courses;
    }

    public function addCourse(Course $course): static
    {
        if (!$this->courses->contains($course)) {
            $this->courses->add($course);
            $course->setSubject($this);
        }

        return $this;
    }

    public function removeCourse(Course $course): static
    {
        if ($this->courses->removeElement($course)) {
            // set the owning side to null (unless already changed)
            if ($course->getSubject() === $this) {
                $course->setSubject(null);
            }
        }

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
            $total += $exerciseCategory->countExercises();
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
}
