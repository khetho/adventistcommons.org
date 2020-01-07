<?php

namespace App\Product\Form\Type;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PdfsType extends AbstractType
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
               'pdfOffsetFile',
               FileType::class,
               [
                    'label' => 'product.field.pdf_offset_file.label',
                    'required' => false,
                ]
           )
            ->add(
                'pdfDigitalFile',
                FileType::class,
                [
                    'label' => 'product.field.pdf_digital_file.label',
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
