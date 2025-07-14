<?php

namespace App\Entity;

use App\Repository\ChapterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChapterRepository::class)]
class Chapter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, CourseElement>
     */
    #[ORM\OneToMany(targetEntity: CourseElement::class, mappedBy: 'chapter')]
    private Collection $courseElements;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'chapters')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Course $course = null;

    public function __construct()
    {
        $this->courseElements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, CourseElement>
     */
    public function getCourseElements(): Collection
    {
        return $this->courseElements;
    }

    public function addCourseElement(CourseElement $courseElement): static
    {
        if (!$this->courseElements->contains($courseElement)) {
            $this->courseElements->add($courseElement);
            $courseElement->setChapter($this);
        }

        return $this;
    }

    public function removeCourseElement(CourseElement $courseElement): static
    {
        if ($this->courseElements->removeElement($courseElement)) {
            // set the owning side to null (unless already changed)
            if ($courseElement->getChapter() === $this) {
                $courseElement->setChapter(null);
            }
        }

        return $this;
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

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): static
    {
        $this->course = $course;

        return $this;
    }
}
