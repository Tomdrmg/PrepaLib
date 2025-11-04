<?php

namespace App\Entity;

use App\Repository\RevisionElementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RevisionElementRepository::class)]
class RevisionElement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Element $first = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Element $second = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Element $details = null;

    #[ORM\Column]
    private ?int $sortNumber = null;

    /**
     * @var Collection<int, RevisionPref>
     */
    #[ORM\OneToMany(targetEntity: RevisionPref::class, mappedBy: 'revisionElement')]
    private Collection $revisionPrefs;

    /**
     * @var Collection<int, RevisionQuestion>
     */
    #[ORM\OneToMany(targetEntity: RevisionQuestion::class, mappedBy: 'revisionElement', cascade: ['persist', 'remove'])]
    private Collection $questions;

    #[ORM\ManyToOne(inversedBy: 'revisionElements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?RevisionSheet $revisionSheet = null;

    #[ORM\Column(length: 255)]
    private ?string $separatorText = null;

    #[ORM\Column]
    private ?int $style = 0;

    public function __construct()
    {
        $this->revisionPrefs = new ArrayCollection();
        $this->questions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirst(): ?Element
    {
        return $this->first;
    }

    public function setFirst(Element $first): static
    {
        $this->first = $first;

        return $this;
    }

    public function getSecond(): ?Element
    {
        return $this->second;
    }

    public function setSecond(Element $second): static
    {
        $this->second = $second;

        return $this;
    }

    public function getDetails(): ?Element
    {
        return $this->details;
    }

    public function setDetails(?Element $details): static
    {
        $this->details = $details;

        return $this;
    }

    public function getSortNumber(): ?int
    {
        return $this->sortNumber;
    }

    public function setSortNumber(int $sortNumber): static
    {
        $this->sortNumber = $sortNumber;

        return $this;
    }

    /**
     * @return Collection<int, RevisionPref>
     */
    public function getRevisionPrefs(): Collection
    {
        return $this->revisionPrefs;
    }

    public function addRevisionPref(RevisionPref $revisionPref): static
    {
        if (!$this->revisionPrefs->contains($revisionPref)) {
            $this->revisionPrefs->add($revisionPref);
            $revisionPref->setRevisionElement($this);
        }

        return $this;
    }

    public function removeRevisionPref(RevisionPref $revisionPref): static
    {
        if ($this->revisionPrefs->removeElement($revisionPref)) {
            // set the owning side to null (unless already changed)
            if ($revisionPref->getRevisionElement() === $this) {
                $revisionPref->setRevisionElement(null);
            }
        }

        return $this;
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

    /**
     * @return Collection<int, RevisionQuestion>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(RevisionQuestion $question): static
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            $question->setRevisionElement($this);
        }

        return $this;
    }

    public function removeQuestion(RevisionQuestion $question): static
    {
        if ($this->questions->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getRevisionElement() === $this) {
                $question->setRevisionElement(null);
            }
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

    public function getSeparatorText(): ?string
    {
        return $this->separatorText;
    }

    public function setSeparatorText(string $separatorText): static
    {
        $this->separatorText = $separatorText;

        return $this;
    }

    public function getStyle(): ?int
    {
        return $this->style;
    }

    public function setStyle(int $style): static
    {
        $this->style = $style;

        return $this;
    }

    public function getPrefFor(?User $user): ?RevisionPref
    {
        if (!$user) return null;

        $pref = $this->getRevisionPrefs()->filter(function (RevisionPref $pref) use ($user) {
            return $pref->getUser() === $user;
        })->first();

        return $pref ?: null;
    }
}
