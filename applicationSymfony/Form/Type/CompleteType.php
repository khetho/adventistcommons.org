<?php

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType as PasswordFieldType;

class CompleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
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
                    'data' => null,
                    'required' => true,
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
