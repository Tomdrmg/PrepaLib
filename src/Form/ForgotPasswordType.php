<?php

namespace App\Form;

use App\Entity\User;
use App\Form\Model\ForgotPassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ForgotPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => "Email",
                'invalid_message' => "Adresse email invalide.",
                "required" => true,
                'attr' => [
                    'placeholder' => "votre@email.com"
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Recevoir un email"
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ForgotPassword::class
        ]);
    }
}
