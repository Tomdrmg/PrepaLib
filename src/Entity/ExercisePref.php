<?php

namespace App\Entity;

use App\Repository\ExercisePrefRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExercisePrefRepository::class)]
class ExercisePref
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $favorite = null;

    #[ORM\Column]
    private ?bool $todo = null;

    #[ORM\Column(length: 2000)]
    private ?string $comment = null;

    #[ORM\ManyToOne(inversedBy: 'exercisePrefs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'exercisePrefs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Exercise $exercise = null;

    #[ORM\Column]
    private ?bool $done = null;

    #[ORM\Column]
    private ?int $difficulty = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isFavorite(): ?bool
    {
        return $this->favorite;
    }

    public function setFavorite(bool $favorite): static
    {
        $this->favorite = $favorite;

        return $this;
    }

    public function isTodo(): ?bool
    {
        return $this->todo;
    }

    public function setTodo(bool $todo): static
    {
        $this->todo = $todo;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

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

    public function isDone(): ?bool
    {
        return $this->done;
    }

    public function setDone(bool $done): static
    {
        $this->done = $done;

        return $this;
    }

    public function getDifficulty(): ?int
    {
        return $this->difficulty;
    }

    public function setDifficulty(int $difficulty): static
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'done' => $this->isDone(),
            'favorite' => $this->isFavorite(),
            'difficulty' => $this->getDifficulty(),
            'comment' => $this->getComment(),
            'exercise' => $this->getExercise()->getId()
        ];
    }
}
