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
