<?php

namespace App\Form\Model;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DatePickerType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'html5' => false,
            'widget' => 'single_text',
            'format' => 'MM/dd/yyyy',
            'attr' => ['class' => 'input_datepicker', 'readonly' => true]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getParent()
    {
        return DateType::class;
    }
}