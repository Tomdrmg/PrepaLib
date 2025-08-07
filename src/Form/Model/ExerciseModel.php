<?php
namespace App\Form\Model;

use App\Entity\Tag;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class ExerciseModel
{
    public string $title = '';
    public string $statement = '';
    public ?string $solution = null;
    /**
     * @var string[]
     */
    public array $hints = [];
    /**
     * @var ArrayCollection<Tag>
     */
    public ArrayCollection $tags;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }
}
