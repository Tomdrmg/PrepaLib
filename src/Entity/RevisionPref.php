<?php

namespace App\Entity;

use App\Repository\RevisionPrefRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RevisionPrefRepository::class)]
class RevisionPref
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $difficulty = null;

    #[ORM\ManyToOne(inversedBy: 'revisionPrefs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'revisionPrefs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?RevisionElement $revisionElement = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getRevisionElement(): ?RevisionElement
    {
        return $this->revisionElement;
    }

    public function setRevisionElement(?RevisionElement $revisionElement): static
    {
        $this->revisionElement = $revisionElement;

        return $this;
    }
}
