<?php

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType as PasswordFieldType;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'firstName',
                null,
                [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'First name',
                    ],
                ]
            )
            ->add(
                'lastName',
                null,
                [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Last name',
                    ],
                ]
            )
            ->add(
                'email',
                null,
                [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Email Address',
                    ],
                ]
            )
            ->add(
                'plainPassword',
                RepeatedType::class,
                [
                    'type' => PasswordFieldType::class,
                    'first_options' => [
                        'label' => false,
                        'attr' => [
                            'placeholder' => 'Password',
                        ],
                        'help' => 'Your password should be at least 8 characters',
                    ],
                    'second_options' => [
                        'label' => false,
                        'attr' => [
                            'placeholder' => 'Confirm password',
                        ],
                    ],
                ]
            )
            ->add(
                'productNotify',
                null,
                [
                    'label' => 'Notify me when new products are available',
                ]
            )
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'empty_data' => function (FormInterface $form) {
                return new User($form->get('email')->getData());
            },
        ]);
    }
}
