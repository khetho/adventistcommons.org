<?php

namespace App\Product\Form\Type;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IdmlType extends AbstractType
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
                'idmlFile',
                FileType::class,
                [
                    'label' => 'IDML file',
                    'required' => false,
                ]
            )
            ->add(
                'pdfOffsetFile',
                FileType::class,
                [
                    'label' => 'PDF offset file',
                    'required' => false,
                ]
            )
            ->add(
                'pdfDigitalFile',
                FileType::class,
                [
                    'label' => 'PDF digital file',
                    'required' => false,
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
