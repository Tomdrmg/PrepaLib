<?php

namespace App\Entity;

use App\Repository\RevisionQuestionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RevisionQuestionRepository::class)]
class RevisionQuestion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Avec possibilité de mettre %first%, %title%, %second% pour ne pas tout réécrire peut-être ?
    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Element $question = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Element $answer = null;

    #[ORM\ManyToOne(inversedBy: 'questions')]
    private ?RevisionElement $revisionElement = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?Element
    {
        return $this->question;
    }

    public function setQuestion(Element $question): static
    {
        $this->question = $question;

        return $this;
    }

    public function getAnswer(): ?Element
    {
        return $this->answer;
    }

    public function setAnswer(?Element $answer): static
    {
        $this->answer = $answer;

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
