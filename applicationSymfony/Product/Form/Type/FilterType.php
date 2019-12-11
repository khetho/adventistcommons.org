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
                    'required' => false,
                ]
            )
            ->add(
                'serie',
                EntityType::class,
                [
                    'class' => Serie::class,
                    'label' => 'Available in',
                    'required' => false,
                ]
            )
            ->add(
                'audience',
                EntityType::class,
                [
                    'class' => Audience::class,
                    'label' => 'Audience',
                    'required' => false,
                ]
            )
            ->add(
                'author',
                TextType::class,
                [
                    'label' => 'Author',
                    'required' => false,
                ]
            )
            ->add(
                'type',
                DictionaryType::class,
                [
                    'name' => 'product_type',
                    'label' => 'Type',
                    'required' => false,
                ]
            )
            ->add(
                'binding',
                EntityType::class,
                [
                    'class' => Binding::class,
                    'label' => 'Binding',
                    'required' => false,
                ]
            )
            ->add(
                'sort',
                DictionaryType::class,
                [
                    'name' => 'product_sort',
                    'multiple' => false,
                    'expanded' => true,
                    'label' => 'Sort by',
                    'required' => true,
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
