<?php

namespace App\Entity;

use App\Repository\EssentialPartRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EssentialPartRepository::class)]
class EssentialPart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 1000)]
    private ?string $title = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Element $element = null;

    #[ORM\ManyToOne(inversedBy: 'categories')]
    private ?SubjectEssential $essential = null;

    #[ORM\Column]
    private ?int $sortNumber = null;

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

    public function getElement(): ?Element
    {
        return $this->element;
    }

    public function setElement(Element $element): static
    {
        $this->element = $element;

        return $this;
    }

    public function getEssential(): ?SubjectEssential
    {
        return $this->essential;
    }

    public function setEssential(?SubjectEssential $essential): static
    {
        $this->essential = $essential;

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
}
