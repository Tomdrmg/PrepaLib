<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => "Prénom",
                "required" => true,
                'attr' => [
                    'placeholder' => "Votre prénom"
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => "Nom",
                "required" => true,
                'attr' => [
                    'placeholder' => "Votre nom"
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => "Email",
                'invalid_message' => "Adresse email invalide.",
                "required" => true,
                'attr' => [
                    'placeholder' => "votre@email.com"
                ]
            ])
            ->add('password', RepeatedType::class, [
                "type" => PasswordType::class,
                'invalid_message' => "Les mots de passe ne correspondent pas.",
                'required' => true,
                'first_options'  => [
                    'label' => "Mot de passe",
                    'required' => true,
                    'attr' => [
                        'placeholder' => "Votre mot de passe"
                    ]
                ],
                'second_options' => [
                    'label' => "Confirmation",
                    'required' => true,
                    'attr' => [
                        'placeholder' => "Votre mot de passe"
                    ]
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Créer mon compte"
            ]);
    }
}
