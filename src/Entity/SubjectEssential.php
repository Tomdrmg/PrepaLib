<?php

namespace App\Entity;

use App\Repository\SubjectEssentialRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubjectEssentialRepository::class)]
class SubjectEssential
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'essential')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Subject $subject = null;

    /**
     * @var Collection<int, EssentialPart>
     */
    #[ORM\OneToMany(targetEntity: EssentialPart::class, mappedBy: 'essential', cascade: ['persist', 'remove'])]
    private Collection $parts;

    public function __construct()
    {
        $this->parts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    public function setSubject(Subject $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return Collection<int, EssentialPart>
     */
    public function getParts(): Collection
    {
        return $this->parts;
    }

    public function addPart(EssentialPart $category): static
    {
        if (!$this->parts->contains($category)) {
            $this->parts->add($category);
            $category->setEssential($this);
        }

        return $this;
    }

    public function removePart(EssentialPart $category): static
    {
        if ($this->parts->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getEssential() === $this) {
                $category->setEssential(null);
            }
        }

        return $this;
    }
}
