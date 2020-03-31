<?php

namespace App\Product\Form\Type;

use App\Entity\Audience;
use App\Entity\Binding;
use App\Entity\Language;
use App\Entity\Series;
use App\Product\Filter\FilterStatus;
use App\Repository\LanguageRepository;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class FilterType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
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
                'title',
                TextType::class,
                [
                    'label' => 'product.filter.title.label',
                    'required' => false,
                ]
            )
            ->add(
                'language',
                EntityType::class,
                [
                    'class' => Language::class,
                    'label' => 'product.filter.language.label',
                    'required' => false,
                    'query_builder' => function (LanguageRepository $repo) {
                        return $this->security->isGranted('ROLE_ADMIN')
                            ? $repo->findUsedInProjectQueryBuilder()
                            : $repo->findUsedInEnabledProjectQueryBuilder();
                    },
                ]
            )
            ->add(
                'audience',
                EntityType::class,
                [
                    'class' => Audience::class,
                    'choice_translation_domain' => false,
                    'label' => 'product.filter.audience.label',
                    'required' => false,
                ]
            )
            ->add(
                'author',
                TextType::class,
                [
                    'label' => 'product.filter.author.label',
                    'required' => false,
                ]
            )
            ->add(
                'type',
                DictionaryType::class,
                [
                    'label' => 'product.filter.type.label',
                    'name' => 'product_type',
                    'required' => false,
                ]
            )
            ->add(
                'binding',
                EntityType::class,
                [
                    'class' => Binding::class,
                    'label' => 'product.filter.binding.label',
                    'required' => false,
                ]
            )
            ->add(
                'sort',
                DictionaryType::class,
                [
                    'name' => 'product_sort',
                    'multiple' => false,
                    'expanded' => true,
                    'label' => 'product.sort.label',
                    'required' => true,
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FilterStatus::class,
        ]);
    }
}
