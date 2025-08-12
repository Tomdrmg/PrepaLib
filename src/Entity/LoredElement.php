<?php

namespace App\Entity;

use App\Repository\LoredElementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LoredElementRepository::class)]
class LoredElement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Element $element = null;

    #[ORM\Column(length: 255)]
    private ?string $lore = null;

    #[ORM\ManyToOne(inversedBy: 'hints')]
    private ?Exercise $hintFor = null;

    #[ORM\ManyToOne(inversedBy: 'shortAnswers')]
    private ?Exercise $answerFor = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLore(): ?string
    {
        return $this->lore;
    }

    public function setLore(string $lore): static
    {
        $this->lore = $lore;

        return $this;
    }

    public function getHintFor(): ?Exercise
    {
        return $this->hintFor;
    }

    public function setHintFor(?Exercise $hintFor): static
    {
        $this->hintFor = $hintFor;

        return $this;
    }

    public function getAnswerFor(): ?Exercise
    {
        return $this->answerFor;
    }

    public function setAnswerFor(?Exercise $answerFor): static
    {
        $this->answerFor = $answerFor;

        return $this;
    }
}
