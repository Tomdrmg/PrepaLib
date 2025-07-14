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

    /**
     * @var Collection<int, ExerciseGroup>
     */
    #[ORM\OneToMany(targetEntity: ExerciseGroup::class, mappedBy: 'subject')]
    private Collection $exerciseGroups;

    public function __construct()
    {
        $this->courses = new ArrayCollection();
        $this->exerciseGroups = new ArrayCollection();
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

    /**
     * @return Collection<int, ExerciseGroup>
     */
    public function getExerciseGroups(): Collection
    {
        return $this->exerciseGroups;
    }

    public function addExerciseGroup(ExerciseGroup $exerciseGroup): static
    {
        if (!$this->exerciseGroups->contains($exerciseGroup)) {
            $this->exerciseGroups->add($exerciseGroup);
            $exerciseGroup->setSubject($this);
        }

        return $this;
    }

    public function removeExerciseGroup(ExerciseGroup $exerciseGroup): static
    {
        if ($this->exerciseGroups->removeElement($exerciseGroup)) {
            // set the owning side to null (unless already changed)
            if ($exerciseGroup->getSubject() === $this) {
                $exerciseGroup->setSubject(null);
            }
        }

        return $this;
    }
}
