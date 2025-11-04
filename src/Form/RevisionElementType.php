<?php

namespace App\Form;

use App\Entity\Element;
use App\Entity\RevisionElement;
use App\Entity\RevisionSheet;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RevisionElementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sortNumber', NumberType::class, [
                'label' => 'Priorité pour le tri'
            ])
            ->add('separatorText', TextType::class, [
                'label' => 'Séparateur',
                'empty_data' => '',
                'required' => false
            ])
            ->add('style', NumberType::class, [
                'label' => 'Style (0 ou 1)'
            ])
            ->add('first', ElementType::class, [
                'label' => 'titre',
                'input_class' => '',
                'required' => false
            ])
            ->add('second', ElementType::class, [
                'label' => 'Description',
                'input_class' => ''
            ])
            ->add('details', ElementType::class, [
                'label' => 'Détails',
                'required' => false
            ])
            ->add('questions', CollectionType::class, [
                'entry_type' => RevisionQuestionType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RevisionElement::class,
        ]);
    }
}
