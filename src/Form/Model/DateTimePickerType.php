<?php

namespace App\Form\Model;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateTimePickerType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'html5' => false,
            'widget' => 'single_text',
            'format' => 'MM/dd/yyyy HH:mm',
            'attr' => [
                'class' => 'input_datetimepicker',
                'readonly' => true
            ]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getParent()
    {
        return DateTimeType::class;
    }
}