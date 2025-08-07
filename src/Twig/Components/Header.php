<?php

namespace App\Twig\Components;

use App\Entity\Subject;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class Header
{
    public ?User $user = null;

    public bool $admin = false;

    /**
     * @var Subject[]
     */
    public array $subjects = [];

    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly Security $security)
    {

    }

    public function mount(): void
    {
        $this->subjects = $this->entityManager->getRepository(Subject::class)->findAll();
        $this->user = $this->security->getUser();
    }
}
