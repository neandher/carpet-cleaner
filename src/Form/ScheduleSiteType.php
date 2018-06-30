<?php

namespace App\Form;

use App\Entity\Schedule;
use App\Form\Model\DatePickerType;
use App\Form\Model\DateTimePickerType;
use App\Form\Model\TimePickerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Time;
use Symfony\Component\Validator\Constraints\Valid;

class ScheduleSiteType extends AbstractType
{
    /**
     * @var SessionInterface
     */
    private $session;


    /**
     * ScheduleSiteType constructor.
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DatePickerType::class, [
                'label' => 'schedule.fields.date',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(),
                    new Date()
                ]
            ])
            ->add('startTime', TimePickerType::class, [
                'label' => 'schedule.fields.startTime',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(),
                    new Time()
                ]
            ])
            ->add('endTime', TimePickerType::class, [
                'label' => 'schedule.fields.endTime',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(),
                    new Time()
                ]
            ])
            ->add('startDateAt', HiddenType::class)
            ->add('endDateAt', HiddenType::class)
            ->add('instructions', TextareaType::class, [
                'label' => 'schedule.fields.instructions',
                'required' => false
            ]);

        if (!$options['hasPhoneNumber']) {
            $builder->add('customer', CustomerType::class, [
                'label' => 'customer.title_single',
                'constraints' => [
                    new Valid()
                ]
            ]);
        }

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($options) {
            $schedule = $event->getData();

            $startDateAt = \DateTime::createFromFormat(
                'm/d/Y H:i',
                $schedule['date'] . ' ' . $schedule['startTime']
            );

            $endDateAt = \DateTime::createFromFormat(
                'm/d/Y H:i',
                $schedule['date'] . ' ' . $schedule['endTime']
            );

            $schedule['startDateAt'] = $startDateAt;
            $schedule['endDateAt'] = $endDateAt;

            if (!$options['hasPhoneNumber']) {
                $schedule['customer']['customerAddresses'][0]['address']['zipCode'] = $this->session->get('zipCode');
            }

            $event->setData($schedule);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Schedule::class,
            'hasPhoneNumber' => false
        ]);
    }
}
