<?php

namespace App\Form;

use App\Entity\Exercise;
use App\Entity\ExerciseCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
            ->add('sortNumber', NumberType::class, [
                'label' => 'Priorité pour le tri (numéro de l\'exercice)'
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre de l’exercice'
            ])
            ->add('statement', ElementType::class, [
                'label' => 'Énoncé (LaTeX)',
            ])
            ->add('solution', ElementType::class, [
                'label' => 'Solution (LaTeX)',
            ])
            ->add('shortAnswers', CollectionType::class, [
                'label' => 'Réponses Courtes',
                'entry_type' => LoredElementType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ])
            ->add('hints', CollectionType::class, [
                'label' => 'Indices',
                'entry_type' => LoredElementType::class,
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
            'data_class' => Exercise::class,
        ]);
    }
}
