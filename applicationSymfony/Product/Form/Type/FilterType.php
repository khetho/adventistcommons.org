<?php

namespace App\Product\Form\Type;

use App\Entity\Audience;
use App\Entity\Binding;
use App\Entity\Serie;
use App\Product\Entity\FilterStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'Original title',
                ]
            )
            ->add(
                'serie',
                EntityType::class,
                [
                    'class' => Serie::class,
                    'label' => 'Available in',
                ]
            )
            ->add(
                'audience',
                EntityType::class,
                [
                    'class' => Audience::class,
                    'label' => 'Audience',
                ]
            )
            ->add(
                'author',
                TextType::class,
                [
                    'label' => 'Author',
                ]
            )
            ->add(
                'type',
                TextType::class,
                [
                    'label' => 'Type',
                ]
            )
            ->add(
                'binding',
                EntityType::class,
                [
                    'class' => Binding::class,
                    'label' => 'Binding',
                ]
            )
            ->add(
                'sort',
                ChoiceType::class,
                [
                    'multiple' => false,
                    'expanded' => true,
                    'choices' => [
                        'Title' => 'title',
                        'Author' => 'author',
                        'Publisher' => 'publisher',
                    ],
                    'label' => 'Sort by',
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FilterStatus::class,
        ]);
    }
}
