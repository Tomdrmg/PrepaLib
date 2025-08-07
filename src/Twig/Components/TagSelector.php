<?php

namespace App\Twig\Components;

use Symfony\Component\Form\FormView;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class TagSelector
{
    /**
     * @var Tag[]
     */
    public array $inheritedTags = [];

    public FormView $field;

    public bool $disableInheritedTags = false;

    public bool $hideLabel = false;

    public string $selectorStyle = "";

    public ?string $onUpdate = null;
}
