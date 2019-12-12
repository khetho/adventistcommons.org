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

class IdmlType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'idmlFile',
                FileType::class,
                [
                    'label' => 'IDML file',
                    'mapped' => false,
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
