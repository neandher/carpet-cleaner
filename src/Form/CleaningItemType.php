<?php

namespace App\Form;

use App\Entity\CleaningItem;
use App\Entity\CleaningItemCategory;
use App\Entity\CleaningItemOption;
use App\Entity\CleaningItemOptions;
use App\Form\Model\ToggleableType;
use App\Repository\CleaningItemCategoryRepository;
use App\Repository\CleaningItemOptionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CleaningItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label' => 'resource.fields.title'])
            ->add('cleaningItemCategory', EntityType::class, [
                'class' => CleaningItemCategory::class,
                'query_builder' => function (CleaningItemCategoryRepository $er) {
                    return $er->queryLatestForm();
                },
                'choice_label' => 'title',
                'label' => 'cleaningItemCategory.title_single',
            ])
            ->add('maxQuantity', NumberType::class, ['label' => 'cleaningItem.fields.maxQuantity'])
            ->add('displayOrder', NumberType::class, ['label' => 'cleaningItem.fields.displayOrder'])
            ->add('toggleable', ToggleableType::class)
            ->add('cleaningItemOptions', CollectionType::class, [
                'entry_type' => CleaningItemOptionsType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'cleaningItemOption.title_single',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CleaningItem::class,
        ]);
    }
}
