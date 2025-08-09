<?php
namespace App\Form\Model;

use App\Entity\Tag;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class HintModel
{
    public ?string $lore = '';
    public string $content = '';
}
