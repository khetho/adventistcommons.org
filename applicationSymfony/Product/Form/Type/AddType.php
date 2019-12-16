<?php

namespace App\Product\Form\Type;

use AdventistCommons\Basics\StringFunctions;
use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\SubmitEvent;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddType extends AbstractType
{
    private $stringFunctions;
    private $generalType;
    private $specsType;
    private $idmlType;

    public function __construct(
        StringFunctions $stringFunctions,
        GeneralType $generalType,
        SpecsType $specsType,
        IdmlType $idmlType
    ) {
        $this->stringFunctions = $stringFunctions;
        $this->generalType = $generalType;
        $this->specsType = $specsType;
        $this->idmlType = $idmlType;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $stringFunctions = $this->stringFunctions;
        $this->generalType->buildForm($builder, []);
        $this->specsType->buildForm($builder, []);
        $this->idmlType->buildForm($builder, []);
        $builder
            ->addEventListener(FormEvents::SUBMIT, function (SubmitEvent $event) use ($stringFunctions) {
                $product = $event->getData();
                if (!$product->getSlug()) {
                    $product->setSlug(
                        $stringFunctions->slugify(
                            $product->getName()
                        )
                    );
                }
                $event->setData($product);
            });
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
