<?php

namespace App\Product\Form\Type;

use App\Entity\Attachment;
use App\Entity\Language;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\SubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddAttachmentType extends AbstractType
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
                'fileType',
                DictionaryType::class,
                [
                    'name' => 'file_type',
                    'label' => 'attachment.field.type.label',
                ]
            )
            ->add(
                'language',
                EntityType::class,
                [
                    'class' => Language::class,
                    'label' => 'attachment.field.language.label',
                ]
            )
            ->add(
                'file',
                FileType::class,
                [
                    'label' => 'attachment.field.file.label',
                    'required' => true,
                ]
            )
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Attachment::class,
        ]);
    }
}
