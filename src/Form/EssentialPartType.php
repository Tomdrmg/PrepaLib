<?php

namespace App\Form;

use App\Entity\EssentialPart;
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

class EssentialPartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sortNumber', NumberType::class, [
                'label' => 'Priorité pour le tri'
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre de la partie'
            ])
            ->add('element', ElementType::class, [
                'label' => 'Contenu (LaTeX)'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EssentialPart::class,
        ]);
    }
}
