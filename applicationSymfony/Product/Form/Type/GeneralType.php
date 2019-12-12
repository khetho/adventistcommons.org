<?php

namespace App\Product\Form\Type;

use App\Entity\Audience;
use App\Entity\Binding;
use App\Entity\Product;
use App\Entity\Serie;
use App\Product\Entity\FilterStatus;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GeneralType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'coverImage',
                FileType::class,
                [
                    'label' => 'Cover image',
                    'mapped' => false,
                    'required' => false,
                ]
            )
            ->add(
                'name',
                null,
                [
                    'label' => 'Title',
                ]
            )
            ->add(
                'author',
                null,
                [
                    'label' => 'Author',
                ]
            )
            ->add(
                'publisher',
                null,
                [
                    'label' => 'Publisher',
                ]
            )
            ->add(
                'publisherWebsite',
                null,
                [
                    'label' => 'Publisher Website',
                ]
            )
            ->add(
                'description',
                null,
                [
                    'label' => 'Description',
                ]
            )
            ->add(
                'pageCount',
                null,
                [
                    'label' => 'Page count',
                ]
            )
            ->add(
                'series',
                null,
                [
                    'label' => 'Series',
                ]
            )
            ->add(
                'audiences',
                null,
                [
                    'label' => 'Audience',
                ]
            )
            ->add(
                'type',
                DictionaryType::class,
                [
                    'name' => 'product_type',
                    'label' => 'Type',
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
