<?php
namespace App\Form;

use App\Form\Model\HintModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HintType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lore', TextType::class, [
                'label' => 'Micro Description',
                'attr' => ['maxlength' => 255],
                'required' => false,
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu (LaTeX)',
                'attr' => ['rows' => 3, 'class' => 'latex-input']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => HintModel::class,
        ]);
    }
}
