<?php

namespace App\Product\Form\Type;

use App\Entity\Audience;
use App\Entity\Binding;
use App\Entity\Product;
use App\Entity\Series;
use App\Product\Entity\FilterStatus;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpecsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'formatClosed',
                null,
                [
                    'label' => 'Closed',
                    'attr' => [
                        'placeholder' => 'e.g. 10.4 x 20.5 cm',
                    ],
                ]
            )
            ->add(
                'formatOpen',
                null,
                [
                    'label' => 'Opened',
                    'attr' => [
                        'placeholder' => 'e.g. 10.4 x 41 cm',
                    ],
                ]
            )
            ->add(
                'coverColors',
                null,
                [
                    'label' => 'Colors',
                ]
            )
            ->add(
                'coverPaper',
                null,
                [
                    'label' => 'Paper',
                ]
            )
            ->add(
                'interiorColors',
                null,
                [
                    'label' => 'Colors',
                ]
            )
            ->add(
                'interiorPaper',
                null,
                [
                    'label' => 'Paper',
                ]
            )
            ->add(
                'binding',
                null,
                [
                    'label' => 'Binding',
                ]
            )
            ->add(
                'finishing',
                null,
                [
                    'label' => 'Finishing',
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
