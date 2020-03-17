<?php

namespace App\Form\Type;

use App\Entity\User;
use App\Entity\Skill;
use App\Form\DataTransformer\ChoicesToValuesAddAllowedTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\ChoiceList\Factory\ChoiceListFactoryInterface;

class SkillsAdder
{
    private $entityManager;
    private $choiceListFactory;

    public function __construct(
        EntityManagerInterface $entityManager,
        ChoiceListFactoryInterface $choiceListFactory
    ) {
        $this->entityManager = $entityManager;
        $this->choiceListFactory = $choiceListFactory;
    }
    
    public function add(FormBuilderInterface $builder, ?User $user)
    {
        $skillChoices = array_merge(
            $this->entityManager->getRepository(Skill::class)->findAll(),
            $user ? $user->getSkillsAdded() : []
        );
        natcasesort($skillChoices);

        $builder
            ->add(
                'skills',
                ChoiceType::class,
                [
                    'multiple' => true,
                    'choices' => $skillChoices,
                    'choice_label' => function ($choice) {
                        return (string) $choice;
                    },
                    'label' => 'account.field.skills.label',
                    'attr' => [
                        'class' => 'skills-select',
                        'placeholder' => 'account.field.skills.placeholder',
                    ],
                    'required' => false,
                ]
            )
        ;

        $builder->get('skills')->resetViewTransformers();
        $skillChoiceList = $this->choiceListFactory->createListFromChoices($skillChoices);
        $builder->get('skills')->addViewTransformer(new ChoicesToValuesAddAllowedTransformer($skillChoiceList));
    }
}
