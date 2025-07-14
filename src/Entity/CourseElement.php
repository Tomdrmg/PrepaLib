<?php

namespace App\Entity;

use App\Repository\CourseElementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CourseElementRepository::class)]
class CourseElement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Element $statement = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Element $proof = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?TagList $tags = null;

    #[ORM\ManyToOne(inversedBy: 'courseElements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Chapter $chapter = null;

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

    public function getProof(): ?Element
    {
        return $this->proof;
    }

    public function setProof(?Element $proof): static
    {
        $this->proof = $proof;

        return $this;
    }

    public function getTags(): ?TagList
    {
        return $this->tags;
    }

    public function setTags(TagList $tags): static
    {
        $this->tags = $tags;

        return $this;
    }

    public function getChapter(): ?Chapter
    {
        return $this->chapter;
    }

    public function setChapter(?Chapter $chapter): static
    {
        $this->chapter = $chapter;

        return $this;
    }
}
