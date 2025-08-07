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

    #[ORM\OneToOne()]
    #[ORM\JoinColumn(nullable: false)]
    private ?Element $statement = null;

    #[ORM\OneToOne()]
    #[ORM\JoinColumn(nullable: false)]
    private ?Element $solution = null;

    /**
     * @var Collection<int, ExercisePref>
     */
    #[ORM\OneToMany(targetEntity: ExercisePref::class, mappedBy: 'exercise')]
    private Collection $exercisePrefs;

    #[ORM\ManyToOne(inversedBy: 'exercises')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ExerciseCategory $category = null;

    /**
     * @var Collection<int, Element>
     */
    #[ORM\OneToMany(targetEntity: Element::class, mappedBy: 'exercise')]
    private Collection $hints;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'exercises')]
    private Collection $tags;

    public function __construct()
    {
        $this->exercisePrefs = new ArrayCollection();
        $this->hints = new ArrayCollection();
        $this->tags = new ArrayCollection();
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

    public function getCategory(): ?ExerciseCategory
    {
        return $this->category;
    }

    public function setCategory(?ExerciseCategory $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Element>
     */
    public function getHints(): Collection
    {
        return $this->hints;
    }

    public function addHint(Element $hint): static
    {
        if (!$this->hints->contains($hint)) {
            $this->hints->add($hint);
            $hint->setExercise($this);
        }

        return $this;
    }

    public function removeHint(Element $hint): static
    {
        if ($this->hints->removeElement($hint)) {
            // set the owning side to null (unless already changed)
            if ($hint->getExercise() === $this) {
                $hint->setExercise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(Tag $tag): static
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    public function getFullTags(): array
    {
        $allTags = [];
        $tagIds = [];

        if ($this->getCategory()) {
            foreach ($this->getCategory()->getFullTags() as $tag) {
                $tagId = $tag->getId();
                if (!in_array($tagId, $tagIds, true)) {
                    $allTags[] = $tag;
                    $tagIds[] = $tagId;
                }
            }
        }

        foreach ($this->getTags() as $tag) {
            $tagId = $tag->getId();
            if (!in_array($tagId, $tagIds, true)) {
                $allTags[] = $tag;
                $tagIds[] = $tagId;
            }
        }

        return $allTags;
    }

    public function getCategoryPath(): string
    {
        $path = $this->category->getName();
        $category = $this->category->getParent();

        while ($category) {
            $path = $category->getName().' > '.$path;
            $category = $category->getParent();
        }

        return $path;
    }

    public function getFirstCategory(): ExerciseCategory
    {
        $category = $this->getCategory();
        while ($category->getParent()) {
            $category = $category->getParent();
        }

        return $category;
    }

    public function getPrefFor(?User $user): ?ExercisePref
    {
        if (!$user) return null;

        $pref = $this->getExercisePrefs()->filter(function (ExercisePref $pref) use ($user) {
            return $pref->getUser() === $user;
        })->first();

        return $pref ? $pref : null;
    }
}
