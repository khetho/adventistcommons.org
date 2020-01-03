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

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'firstName',
                null,
                [
                    'label' => 'account.field.firstname.label',
                    'attr' => [
                        'placeholder' => 'account.field.firstname.placeholder',
                    ],
                ]
            )
            ->add(
                'lastName',
                null,
                [
                    'label' => 'account.field.lastname.label',
                    'attr' => [
                        'placeholder' => 'account.field.lastname.placeholder',
                    ],
                ]
            )
            ->add(
                'email',
                null,
                [
                    'label' => 'account.field.email.label',
                    'attr' => [
                        'placeholder' => 'account.field.email.placeholder',
                    ],
                ]
            )
            ->add(
                'location',
                null,
                [
                    'label' => 'account.field.location.label',
                    'attr' => [
                        'placeholder' => 'account.field.location.placeholder',
                    ]
                ]
            )
            ->add(
                'bio',
                null,
                [
                    'label' => 'account.field.bio.label',
                    'attr' => [
                        'placeholder' => 'account.field.bio.placeholder',
                    ],
                    'help' => 'account.field.bio.help',
                ]
            )
            ->add(
                'motherLanguage',
                null,
                [
                    'label' => 'account.field.motherLanguage.label',
                    'attr' => [
                        'class' => 'language-select',
                        'placeholder' => 'account.field.motherLanguage.placeholder',
                    ],
                ]
            )
            ->add(
                'languages',
                null,
                [
                    'label' => 'account.field.languages.label',
                    'attr' => [
                        'class' => 'language-select',
                        'placeholder' => 'account.field.languages.placeholder',
                    ],
                ]
            )
            ->add(
                'proTranslator',
                null,
                [
                    'label' => 'account.field.proTranslator.label',
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
