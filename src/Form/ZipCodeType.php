<?php

namespace App\Form;

use App\Entity\ZipCode;
use App\Form\Model\ToggleableType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ZipCodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', NumberType::class, ['label' => 'resource.fields.description'])
            ->add('percentage', PercentType::class, ['label' => 'zipCode.fields.percentage'])
            ->add('toggleable', ToggleableType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ZipCode::class,
        ]);
    }
}
