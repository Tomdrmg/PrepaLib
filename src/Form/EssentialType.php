<?php

namespace App\Form;

use App\Entity\Exercise;
use App\Entity\ExerciseCategory;
use App\Entity\SubjectEssential;
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

class EssentialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('parts', CollectionType::class, [
                'label' => 'Parties',
                'entry_type' => EssentialPartType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SubjectEssential::class,
        ]);
    }
}
