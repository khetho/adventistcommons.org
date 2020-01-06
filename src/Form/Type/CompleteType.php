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
    private $skillsAdder;

    public function __construct(
        SkillsAdder $skillsAdder
    ) {
        $this->skillsAdder = $skillsAdder;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
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
                    ]
                ]
            )
            ->add(
                'motherLanguage',
                null,
                [
                    'label' => 'account.field.motherLanguage.label',
                    'data' => null,
                    'required' => true,
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
            )
        ;

        $this->skillsAdder->add($builder, $options['data']);
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
