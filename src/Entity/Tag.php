<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $color = null;

    #[ORM\ManyToOne(inversedBy: 'tags')]
    private ?TagList $tagList = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getColor(): ?int
    {
        return $this->color;
    }

    public function setColor(int $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getTagList(): ?TagList
    {
        return $this->tagList;
    }

    public function setTagList(?TagList $tagList): static
    {
        $this->tagList = $tagList;

        return $this;
    }
}
