<?php

namespace App\Form;

use App\Entity\CleaningItemOption;
use App\Entity\CleaningItemOptions;
use App\Form\Model\SwitchType;
use App\Repository\CleaningItemOptionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CleaningItemOptionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('percentage', PercentType::class, ['label' => 'cleaningItemOptions.fields.percentage'])
            ->add('isEnabled', SwitchType::class, ['label' => 'resource.toglleable.is_enabled_title'])
            ->add('cleaningItemOption', EntityType::class, [
                'class' => CleaningItemOption::class,
                'query_builder' => function (CleaningItemOptionRepository $er) {
                    return $er->queryLatestForm();
                },
                'label' => 'cleaningItemOption.title_single'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CleaningItemOptions::class,
        ]);
    }
}
