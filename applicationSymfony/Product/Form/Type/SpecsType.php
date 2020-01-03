<?php

namespace App\Product\Form\Type;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpecsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'formatClosed',
                null,
                [
                    'label' => 'product.field.formatClosed.label',
                    'attr' => [
                        'placeholder' => 'product.field.formatClosed.placeholder',
                    ],
                ]
            )
            ->add(
                'formatOpen',
                null,
                [
                    'label' => 'product.field.formatOpen.label',
                    'attr' => [
                        'placeholder' => 'product.field.formatClosed.placeholder',
                    ],
                ]
            )
            ->add(
                'coverColors',
                null,
                [
                    'label' => 'product.field.coverColors.label',
                ]
            )
            ->add(
                'coverPaper',
                null,
                [
                    'label' => 'product.field.coverPaper.label',
                ]
            )
            ->add(
                'interiorColors',
                null,
                [
                    'label' => 'product.field.interiorColors.label',
                ]
            )
            ->add(
                'interiorPaper',
                null,
                [
                    'label' => 'product.field.interiorPaper.label',
                ]
            )
            ->add(
                'binding',
                null,
                [
                    'label' => 'product.field.binding.label',
                ]
            )
            ->add(
                'finishing',
                null,
                [
                    'label' => 'product.field.finishing.label',
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
