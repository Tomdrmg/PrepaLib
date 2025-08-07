<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Tag;
use App\Form\Model\ExerciseModel;

class ExerciseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de l’exercice'
            ])
            ->add('statement', TextareaType::class, [
                'label' => 'Énoncé (LaTeX)',
                'attr' => ['rows' => 6, 'class' => 'latex-input']
            ])
            ->add('solution', TextareaType::class, [
                'label' => 'Solution (LaTeX)',
                'attr' => ['rows' => 6, 'class' => 'latex-input'],
                'required' => false
            ])
            ->add('hints', CollectionType::class, [
                'label' => 'Indices (LaTeX)',
                'entry_type' => TextareaType::class,
                'entry_options' => [
                    'label' => false,
                    'attr' => ['rows' => 3, 'class' => 'latex-input']
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ])
            ->add('tags', EntityType::class, [
                'label' => 'Tags',
                'class' => Tag::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer l\'exercice',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExerciseModel::class,
        ]);
    }
}
