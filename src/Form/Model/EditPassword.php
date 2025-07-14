<?php
namespace App\Form\Model;

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;

class EditPassword
{
    /**
     * @SecurityAssert\UserPassword(
     *     message = "Mauvais mot de passe actuel"
     * )
     */
    public $oldPassword;

    /**
     * @Assert\NotBlank()
     */
    public $newPassword;
}
