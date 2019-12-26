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
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'previousPassword',
                PasswordFieldType::class,
                [
                    'label' => 'account.field.current_password.label',
                    'mapped' => 'false',
                    'attr' => [
                        'placeholder' => 'account.field.current_password.placeholder',
                    ],
                ]
            )
            ->add(
                'newPassword',
                RepeatedType::class,
                [
                    'type' => PasswordFieldType::class,
                    'first_options' => [
                        'label' => 'account.field.new_password.label',
                        'attr' => [
                            'placeholder' => 'account.field.new_password.placeholder',
                        ],
                    ],
                    'second_options' => [
                        'label' => 'account.field.confirm_password.label',
                        'attr' => [
                            'placeholder' => 'account.field.confirm_password.placeholder',
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
