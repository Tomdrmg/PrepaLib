<?php

namespace App\Entity;

use App\Repository\QuizDataRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuizDataRepository::class)]
class QuizData
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, RevisionSheet>
     */
    #[ORM\ManyToMany(targetEntity: RevisionSheet::class)]
    private Collection $sheets;

    #[ORM\Column]
    private ?int $questionCount = 5;

    #[ORM\Column]
    private ?int $unknownWeight = 0;

    #[ORM\Column]
    private ?int $familiarWeight = 0;

    #[ORM\Column]
    private ?int $knownWeight = 0;

    #[ORM\Column]
    private ?int $masteredWeight = 0;

    #[ORM\OneToOne(inversedBy: 'quizData')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private ?int $neverSeenWeight = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?subject $subject = null;

    public function __construct()
    {
        $this->sheets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, RevisionSheet>
     */
    public function getSheets(): Collection
    {
        return $this->sheets;
    }

    public function addSheet(RevisionSheet $sheet): static
    {
        if (!$this->sheets->contains($sheet)) {
            $this->sheets->add($sheet);
        }

        return $this;
    }

    public function removeSheet(RevisionSheet $sheet): static
    {
        $this->sheets->removeElement($sheet);

        return $this;
    }

    public function getQuestionCount(): ?int
    {
        return $this->questionCount;
    }

    public function setQuestionCount(int $questionCount): static
    {
        $this->questionCount = $questionCount;

        return $this;
    }

    public function getUnknownWeight(): ?int
    {
        return $this->unknownWeight;
    }

    public function setUnknownWeight(int $unknownWeight): static
    {
        $this->unknownWeight = $unknownWeight;

        return $this;
    }

    public function getFamiliarWeight(): ?int
    {
        return $this->familiarWeight;
    }

    public function setFamiliarWeight(int $familiarWeight): static
    {
        $this->familiarWeight = $familiarWeight;

        return $this;
    }

    public function getKnownWeight(): ?int
    {
        return $this->knownWeight;
    }

    public function setKnownWeight(int $knownWeight): static
    {
        $this->knownWeight = $knownWeight;

        return $this;
    }

    public function getMasteredWeight(): ?int
    {
        return $this->masteredWeight;
    }

    public function setMasteredWeight(int $masteredWeight): static
    {
        $this->masteredWeight = $masteredWeight;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getNeverSeenWeight(): ?int
    {
        return $this->neverSeenWeight;
    }

    public function setNeverSeenWeight(int $neverSeenWeight): static
    {
        $this->neverSeenWeight = $neverSeenWeight;

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
}
