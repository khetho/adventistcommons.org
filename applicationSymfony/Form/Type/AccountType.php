<?php

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('email')
            ->add(
                'location',
                null,
                [
                    'attr' => [
                        'placeholder' => 'Enter location',
                    ]
                ]
            )
            ->add(
                'bio',
                null,
                [
                    'attr' => [
                        'placeholder' => 'Tell us a little about yourself',
                    ]
                ]
            )
            ->add(
                'motherLanguage',
                null,
                [
                    'attr' => [
                        'class' => 'language-select',
                        'placeholder' => 'Select language…',
                    ],
                ]
            )
            ->add(
                'languages',
                null,
                [
                    'label' => 'Fluent languages',
                    'attr' => [
                        'class' => 'language-select',
                        'placeholder' => 'Select languages…',
                    ],
                ]
            )
            ->add(
                'proTranslator',
                null,
                [
                    'label' => 'I am a professional translator',
                ]
            )
            ->add(
                'skills',
                null,
                [
                    'label' => 'Other skills',
                    'attr' => [
                        'class' => 'skills-select',
                        'placeholder' => 'Select skills…',
                    ],
                ]
            )
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
