<?php

namespace App\Form;

use App\Entity\CleaningItem;
use App\Entity\CleaningItemCategory;
use App\Form\Model\ToggleableType;
use App\Repository\CleaningItemCategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CleaningItem::class,
        ]);
    }
}
