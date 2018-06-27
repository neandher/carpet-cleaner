<?php

namespace App\Form\Model;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimePickerType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'html5' => false,
            'widget' => 'single_text',
            'format' => 'HH:mm',
            'attr' => [
                'class' => 'input_timepicker',
                'readonly' => true
            ]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getParent()
    {
        return TimeType::class;
    }
}