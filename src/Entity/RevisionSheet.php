<?php

namespace App\Entity;

use App\Repository\RevisionSheetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RevisionSheetRepository::class)]
class RevisionSheet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column]
    private ?int $sortNumber = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    private ?self $parent = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parent', cascade: ['persist', 'remove'])]
    private Collection $children;

    #[ORM\ManyToOne(inversedBy: 'revisionSheets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Subject $subject = null;

    /**
     * @var Collection<int, RevisionElement>
     */
    #[ORM\OneToMany(targetEntity: RevisionElement::class, mappedBy: 'revisionSheet', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $revisionElements;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->revisionElements = new ArrayCollection();
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

    public function getSortNumber(): ?int
    {
        return $this->sortNumber;
    }

    public function setSortNumber(int $sortNumber): static
    {
        $this->sortNumber = $sortNumber;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): static
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): static
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    public function getSubject(): ?subject
    {
        return $this->subject;
    }

    public function setSubject(?subject $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function countQuestions(): int
    {
        $total = 0;

        foreach ($this->getRevisionElements() as $element) {
            $total += $element->getQuestions()->count();
        }

        return $total;
    }

    public function countQuestionsRec(): int
    {
        $total = $this->countQuestions();

        foreach ($this->getChildren() as $child) {
            $total += $child->countQuestions();
        }

        return $total;
    }

    public function getRevisionElementsRec(): array
    {
        $elements = [];

        foreach ($this->revisionElements as $element) {
            $elements[] = $element;
        }

        foreach ($this->getChildren() as $sheet) {
            $elements = array_merge($elements, $sheet->getRevisionElementsRec());
        }

        return $elements;
    }

    /**
     * @return Collection<int, RevisionElement>
     */
    public function getRevisionElements(): Collection
    {
        return $this->revisionElements;
    }

    public function addRevisionElement(RevisionElement $revisionElement): static
    {
        if (!$this->revisionElements->contains($revisionElement)) {
            $this->revisionElements->add($revisionElement);
            $revisionElement->setRevisionSheet($this);
        }

        return $this;
    }

    public function removeRevisionElement(RevisionElement $revisionElement): static
    {
        if ($this->revisionElements->removeElement($revisionElement)) {
            // set the owning side to null (unless already changed)
            if ($revisionElement->getRevisionSheet() === $this) {
                $revisionElement->setRevisionSheet(null);
            }
        }

        return $this;
    }

    public function getHighestParent(): RevisionSheet
    {
        return $this->parent === null ? $this : $this->parent->getHighestParent();
    }
}
