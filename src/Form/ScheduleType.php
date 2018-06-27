<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\Schedule;
use App\Form\Model\DateTimePickerType;
use App\Repository\CustomerRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScheduleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateTimePickerType::class, ['label' => 'schedule.fields.date'])
            ->add('instructions', TextareaType::class, ['label' => 'schedule.fields.instructions'])
            ->add('customer', EntityType::class, [
                'class' => Customer::class,
                'query_builder' => function (CustomerRepository $er) {
                    return $er->queryLatestForm();
                },
                'choice_label' => 'fullName',
                'label' => 'customer.title_single',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Schedule::class,
        ]);
    }
}
