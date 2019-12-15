<?php

namespace App\Product\Form\Type;

use AdventistCommons\Basics\StringFunctions;
use App\Entity\Audience;
use App\Entity\Binding;
use App\Entity\Product;
use App\Entity\Series;
use App\Product\Entity\FilterStatus;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Event\SubmitEvent;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class AddType extends AbstractType
{
    private $stringFunctions;
    
    public function __construct(StringFunctions $stringFunctions)
    {
        $this->stringFunctions = $stringFunctions;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $stringFunctions = $this->stringFunctions;
        $builder
            ->add(
                'name',
                null,
                [
                    'label' => 'Title',
                ]
            )
            ->add(
                'author',
                null,
                [
                    'label' => 'Author',
                ]
            )
            ->add(
                'publisher',
                null,
                [
                    'label' => 'Publisher',
                ]
            )
            ->add(
                'publisherWebsite',
                null,
                [
                    'label' => 'Publisher Website',
                ]
            )
            ->add(
                'description',
                null,
                [
                    'label' => 'Description',
                ]
            )
            ->add(
                'audiences',
                null,
                [
                    'label' => 'Audience',
                ]
            )
            ->add(
                'pageCount',
                null,
                [
                    'label' => 'Page count',
                ]
            )
            ->add(
                'series',
                null,
                [
                    'label' => 'Series',
                ]
            )
            ->add(
                'type',
                DictionaryType::class,
                [
                    'name' => 'product_type',
                    'label' => 'Type',
                    'expanded' => true,

                ]
            )
            ->add(
                'formatClosed',
                null,
                [
                    'label' => 'Closed',
                    'attr' => [
                        'placeholder' => 'e.g. 10.4 x 20.5 cm',
                    ],
                ]
            )
            ->add(
                'formatOpen',
                null,
                [
                    'label' => 'Opened',
                    'attr' => [
                        'placeholder' => 'e.g. 10.4 x 41 cm',
                    ],
                ]
            )
            ->add(
                'coverColors',
                null,
                [
                    'label' => 'Colors',
                ]
            )
            ->add(
                'coverPaper',
                null,
                [
                    'label' => 'Paper',
                ]
            )
            ->add(
                'interiorColors',
                null,
                [
                    'label' => 'Colors',
                ]
            )
            ->add(
                'interiorPaper',
                null,
                [
                    'label' => 'Paper',
                ]
            )
            ->add(
                'binding',
                null,
                [
                    'label' => 'Binding',
                ]
            )
            ->add(
                'finishing',
                null,
                [
                    'label' => 'Finishing',
                ]
            )
            ->add(
                'coverImage',
                FileType::class,
                [
                    'label' => 'Cover image',
                    'required' => false,
                    'help' => 'Only square image is allowed',
                ]
            )
            ->add(
                'idmlFile',
                FileType::class,
                [
                    'label' => 'IDML file',
                    'mapped' => false,
                    'required' => false,
                ]
            )
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
