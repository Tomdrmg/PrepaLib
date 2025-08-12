<?php

namespace App\Twig\Components;

use Symfony\Component\Form\FormView;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class ListInput
{
    public string $title;

    public FormView $field;

    public ?string $subClass;

    public bool $une = false;
}
