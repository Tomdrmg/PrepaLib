<?php

namespace App\Entity;

use App\Repository\ElementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ElementRepository::class)]
class Element
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'hints')]
    private ?Exercise $exercise = null;

    #[ORM\ManyToOne(inversedBy: 'shortAnswers')]
    private ?Exercise $exerciseAnswer = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getExercise(): ?Exercise
    {
        return $this->exercise;
    }

    public function setExercise(?Exercise $exercise): static
    {
        $this->exercise = $exercise;

        return $this;
    }

    public function getExerciseAnswer(): ?Exercise
    {
        return $this->exerciseAnswer;
    }

    public function setExerciseAnswer(?Exercise $exerciseAnswer): static
    {
        $this->exerciseAnswer = $exerciseAnswer;

        return $this;
    }
}
