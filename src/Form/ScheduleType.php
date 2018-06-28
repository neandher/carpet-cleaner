<?php

namespace App\Form;

use App\Entity\Schedule;
use App\Form\Model\DateTimePickerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScheduleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDateAt', DateTimePickerType::class, ['label' => 'schedule.fields.startDateAt'])
            ->add('endDateAt', DateTimePickerType::class, ['label' => 'schedule.fields.endDateAt'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Schedule::class,
        ]);
    }
}
