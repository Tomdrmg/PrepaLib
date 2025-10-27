<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    /**
     * @var Collection<int, ExercisePref>
     */
    #[ORM\OneToMany(targetEntity: ExercisePref::class, mappedBy: 'user')]
    private Collection $exercisePrefs;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $resetToken = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $resetTokenRequestedAt = null;

    #[ORM\Column]
    private bool $wantsNews = true;

    /**
     * @var Collection<int, RevisionPref>
     */
    #[ORM\OneToMany(targetEntity: RevisionPref::class, mappedBy: 'user')]
    private Collection $revisionPrefs;

    public function __construct()
    {
        $this->exercisePrefs = new ArrayCollection();
        $this->revisionPrefs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return Collection<int, ExercisePref>
     */
    public function getExercisePrefs(): Collection
    {
        return $this->exercisePrefs;
    }

    public function addExercisePref(ExercisePref $exercisePref): static
    {
        if (!$this->exercisePrefs->contains($exercisePref)) {
            $this->exercisePrefs->add($exercisePref);
            $exercisePref->setUser($this);
        }

        return $this;
    }

    public function removeExercisePref(ExercisePref $exercisePref): static
    {
        if ($this->exercisePrefs->removeElement($exercisePref)) {
            // set the owning side to null (unless already changed)
            if ($exercisePref->getUser() === $this) {
                $exercisePref->setUser(null);
            }
        }

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): static
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    public function getResetTokenRequestedAt(): ?\DateTimeImmutable
    {
        return $this->resetTokenRequestedAt;
    }

    public function setResetTokenRequestedAt(?\DateTimeImmutable $resetTokenRequestedAt): static
    {
        $this->resetTokenRequestedAt = $resetTokenRequestedAt;

        return $this;
    }

    public function isWantsNews(): ?bool
    {
        return $this->wantsNews;
    }

    public function setWantsNews(bool $wantsNews): static
    {
        $this->wantsNews = $wantsNews;

        return $this;
    }

    /**
     * @return Collection<int, RevisionPref>
     */
    public function getRevisionPrefs(): Collection
    {
        return $this->revisionPrefs;
    }

    public function addRevisionPref(RevisionPref $revisionPref): static
    {
        if (!$this->revisionPrefs->contains($revisionPref)) {
            $this->revisionPrefs->add($revisionPref);
            $revisionPref->setUser($this);
        }

        return $this;
    }

    public function removeRevisionPref(RevisionPref $revisionPref): static
    {
        if ($this->revisionPrefs->removeElement($revisionPref)) {
            // set the owning side to null (unless already changed)
            if ($revisionPref->getUser() === $this) {
                $revisionPref->setUser(null);
            }
        }

        return $this;
    }
}
