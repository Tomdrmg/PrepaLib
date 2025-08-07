<?php

namespace App\Twig\Components;

use App\Entity\Subject;
use App\Entity\User;
use Symfony\Component\Form\FormView;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class ExerciseList
{
    public Subject $subject;

    public User $user;

    public FormView $form;
}
