<?php

namespace App\Form\Type;

use App\Form\Model\Password;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType as PasswordFieldType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class PasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'previousPassword',
                PasswordFieldType::class,
                [
                    'label' => 'Current password',
                    'mapped' => 'false',
                    'attr' => [
                        'placeholder' => 'Enter your current password',
                    ],
                ]
            )
            ->add(
                'newPassword',
                RepeatedType::class,
                [
                    'type' => PasswordFieldType::class,
                    'first_options' => [
                        'label' => 'New password',
                        'attr' => [
                            'placeholder' => 'Enter your new password',
                        ],
                    ],
                    'second_options' => [
                        'label' => 'Confirm password',
                        'attr' => [
                            'placeholder' => 'Enter the same password',
                        ],
                    ],
                ]
            )
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Password::class,
        ]);
    }
}
