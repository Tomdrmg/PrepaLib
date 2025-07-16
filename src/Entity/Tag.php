<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @var Collection<int, TagList>
     */
    #[ORM\ManyToMany(targetEntity: TagList::class, mappedBy: 'tags')]
    private Collection $tagLists;

    public function __construct()
    {
        $this->tagLists = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, TagList>
     */
    public function getTagLists(): Collection
    {
        return $this->tagLists;
    }

    public function addTagList(TagList $tagList): static
    {
        if (!$this->tagLists->contains($tagList)) {
            $this->tagLists->add($tagList);
            $tagList->addTag($this);
        }

        return $this;
    }

    public function removeTagList(TagList $tagList): static
    {
        if ($this->tagLists->removeElement($tagList)) {
            $tagList->removeTag($this);
        }

        return $this;
    }
}
