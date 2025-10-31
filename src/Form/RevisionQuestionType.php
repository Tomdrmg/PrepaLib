<?php

namespace App\Form;

use App\Entity\Element;
use App\Entity\RevisionElement;
use App\Entity\RevisionQuestion;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RevisionQuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('question', ElementType::class, [
                'label' => 'Question',
            ])
            ->add('answer', ElementType::class, [
                'label' => 'Réponse',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RevisionQuestion::class,
        ]);
    }
}
