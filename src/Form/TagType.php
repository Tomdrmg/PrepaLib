<?php

namespace App\Form;

use App\Entity\Tag;
use App\Entity\TagList;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', HiddenType::class, [
                'required' => false,
                'mapped' => false
            ])
            ->add('name', TextType::class, [
                'label' => "Nom",
                'required' => true,
                'attr' => [
                    'placeholder' => "Le nom du tag"
                ]
            ])
            ->add('color', ColorType::class, [
                'label' => "Couleur",
                'required' => true,
                'attr' => [
                    'placeholder' => "La couleur du tag"
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Ajouter un nouveau tag",
                'attr' => [
                    'editLabel' => "Mettre à jour le tag"
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tag::class,
        ]);
    }
}
