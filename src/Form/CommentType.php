<?php
namespace App\Form;

use App\Form\Model\HintModel;
use App\Form\Model\TextModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', TextareaType::class, [
                'label' => 'Commentaire',
                'attr' => ['rows' => 3],
                'required' => false
            ])
            ->add('submit', SubmitType::class, ['label' => 'Enregistrer le commentaire']);;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TextModel::class,
        ]);
    }
}
