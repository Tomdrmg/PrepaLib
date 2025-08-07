<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class Tag
{
    public string $name;

    public string $color;

    public bool $withRemoveButton = false;
}
