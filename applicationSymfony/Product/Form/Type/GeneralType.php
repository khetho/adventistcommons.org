<?php

namespace App\Product\Form\Type;

use App\Entity\Product;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GeneralType extends AbstractType
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
                'coverImage',
                FileType::class,
                [
                    'label' => 'product.field.cover_image.label',
                    'required' => false,
                    'help' => 'product.field.cover_image.help',
                ]
            )
            ->add(
                'name',
                null,
                [
                    'label' => 'product.field.title.label',
                ]
            )
            ->add(
                'author',
                null,
                [
                    'label' => 'product.field.author.label',
                ]
            )
            ->add(
                'publisher',
                null,
                [
                    'label' => 'product.field.publisher.label',
                ]
            )
            ->add(
                'publisherWebsite',
                null,
                [
                    'label' => 'product.field.publisher_website.label',
                ]
            )
            ->add(
                'description',
                null,
                [
                    'label' => 'product.field.description.label',
                ]
            )
            ->add(
                'pageCount',
                null,
                [
                    'label' => 'product.field.page_count.label',
                ]
            )
            ->add(
                'series',
                null,
                [
                    'label' => 'product.field.series.label',
                ]
            )
            ->add(
                'audiences',
                null,
                [
                    'label' => 'product.field.audiences.label',
                ]
            )
            ->add(
                'type',
                DictionaryType::class,
                [
                    'name' => 'product_type',
                    'label' => 'product.field.type.label',
                    'expanded' => true,

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
