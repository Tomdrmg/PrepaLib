<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class MenuItem
{
    public string $title = "";
    public string $details = "";
    public string $iconClass = "";
    public string $targetUrl = "";
}
