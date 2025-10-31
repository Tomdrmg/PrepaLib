<?php

namespace App\Form;

use App\Entity\QuizData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class StartQuizType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('neverSeenWeight', NumberType::class, [
                'label' => false,
                'data' => 1,
                'attr' => [
                    'min' => 0,
                ],
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => 'Le poids doit être au moins égal à 0.',
                    ]),
                ],
            ])
            ->add('unknownWeight', NumberType::class, [
                'label' => false,
                'data' => 1,
                'attr' => [
                    'min' => 0,
                ],
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => 'Le poids doit être au moins égal à 0.',
                    ]),
                ],
            ])
            ->add('familiarWeight', NumberType::class, [
                'label' => false,
                'data' => 1,
                'attr' => [
                    'min' => 0,
                ],
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => 'Le poids doit être au moins égal à 0.',
                    ]),
                ],
            ])
            ->add('knownWeight', NumberType::class, [
                'label' => false,
                'data' => 1,
                'attr' => [
                    'min' => 0,
                ],
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => 'Le poids doit être au moins égal à 0.',
                    ]),
                ],
            ])
            ->add('masteredWeight', NumberType::class, [
                'label' => false,
                'data' => 1,
                'attr' => [
                    'min' => 0,
                ],
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => 'Le poids doit être au moins égal à 0.',
                    ]),
                ],
            ])
            ->add('questionCount', NumberType::class, [
                'label' => 'Nombre de questions',
                'data' => 10,
                'attr' => [
                    'min' => 5,
                ],
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 5,
                        'message' => 'Le nombre de questions doit être au moins égal à 5.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QuizData::class
        ]);
    }
}
