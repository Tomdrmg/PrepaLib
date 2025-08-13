<?php
namespace App\Form;

use App\Entity\LoredElement;
use App\Form\Model\LoredElementModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoredElementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lore', TextType::class, [
                'label' => 'Micro Description',
                'empty_data' => '',
                'attr' => ['maxlength' => 255],
                'required' => false,
            ])
            ->add('element', ElementType::class, [
                'label' => 'Contenu (LaTeX)',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LoredElement::class,
        ]);
    }
}
