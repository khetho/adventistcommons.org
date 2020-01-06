<?php

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType as PasswordFieldType;

class RegisterType extends AbstractType
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
                'firstName',
                null,
                [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'account.field.firstname.placeholder',
                    ],
                ]
            )
            ->add(
                'lastName',
                null,
                [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'account.field.lastname.placeholder',
                    ],
                ]
            )
            ->add(
                'email',
                null,
                [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'account.field.email.placeholder',
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
                            'placeholder' => 'account.field.create_password.placeholder',
                        ],
                        'help' => 'account.field.create_password.help',
                    ],
                    'second_options' => [
                        'label' => false,
                        'attr' => [
                            'placeholder' => 'account.field.confirm_password.placeholder',
                        ],
                    ],
                ]
            )
            ->add(
                'productNotify',
                null,
                [
                    'label' => 'account.field.productNotify.label',
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
