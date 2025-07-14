<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class DoubleText
{
    public bool $neon = true;
    public string $text = "";
    public int $lgFont = 16;
    public int $smFont = 12;
}
