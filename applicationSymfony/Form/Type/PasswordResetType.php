<?php

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType as PasswordFieldType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class PasswordResetType extends AbstractType
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
                'plainPassword',
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
            'data_class' => User::class,
        ]);
    }
}
