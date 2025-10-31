<?php

namespace App\Twig\Components;

use App\Entity\RevisionSheet;
use App\Entity\Subject;
use App\Entity\User;
use Symfony\Component\Form\FormView;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class SheetTree
{
    public RevisionSheet $sheet;

    public ?User $user = null;

    public bool $admin = false;

    public ?FormView $form = null;

    public ?string $backPath = null;
}
