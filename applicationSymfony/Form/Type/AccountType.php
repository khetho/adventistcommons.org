<?php

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountType extends AbstractType
{
    private $skillsAdder;

    public function __construct(
        SkillsAdder $skillsAdder
    ) {
        $this->skillsAdder = $skillsAdder;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'firstName',
                null,
                [
                    'attr' => [
                        'placeholder' => 'First name',
                    ],
                ]
            )
            ->add(
                'lastName',
                null,
                [
                    'attr' => [
                        'placeholder' => 'Last name',
                    ],
                ]
            )
            ->add(
                'email',
                null,
                [
                    'attr' => [
                        'placeholder' => 'Email Address',
                    ],
                ]
            )
            ->add(
                'location',
                null,
                [
                    'attr' => [
                        'placeholder' => 'Your location',
                    ]
                ]
            )
            ->add(
                'bio',
                null,
                [
                    'attr' => [
                        'placeholder' => 'Tell us a little about yourself',
                    ],
                    'help' => 'This will be displayed on your public profile',
                ]
            )
            ->add(
                'motherLanguage',
                null,
                [
                    'label' => 'What is your mother language?',
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
                    'label' => 'What other languages are you fluent in?',
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
            );

        $this->skillsAdder->add($builder, $options['data']);
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
