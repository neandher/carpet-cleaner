<?php

namespace App\Form;

use App\Entity\Schedule;
use App\Form\Model\DatePickerType;
use App\Form\Model\DateTimePickerType;
use App\Form\Model\TimePickerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->add('startDateAt', HiddenType::class)
            ->add('endDateAt', HiddenType::class)
            ->add('instructions', TextareaType::class, [
                'label' => 'schedule.fields.instructions',
                'required' => false
            ]);

        $timeChoices = [];

        foreach ($options['times'] as $time) {
            $timeChoices[] = $time['time_start'] . '__' . $time['time_end'];
        }

        $builder->add('times', ChoiceType::class, [
            'mapped' => false,
            'expanded' => true,
            'multiple' => false,
            'choices' => $timeChoices,
            'constraints' => [
                new NotBlank()
            ]
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

            if (isset($schedule['times']) && strstr($schedule['times'], '__')) {
                $times = explode('__', $schedule['times']);
                $startTime = $times[0];
                $endTime = $times[1];

                $startDateAt = \DateTime::createFromFormat(
                    'm/d/Y H:i',
                    $schedule['date'] . ' ' . $startTime
                );

                $endDateAt = \DateTime::createFromFormat(
                    'm/d/Y H:i',
                    $schedule['date'] . ' ' . $endTime
                );

                $schedule['startDateAt'] = $startDateAt;
                $schedule['endDateAt'] = $endDateAt;
            }

            if (!$options['hasPhoneNumber']) {
                $schedule['customer']['customerAddresses'][0]['address']['zipCode'] = $this->session->get('zipCode');
                $schedule['customer']['customerAddresses'][0]['address']['city'] = $this->session->get('city');
            }

            $event->setData($schedule);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Schedule::class,
            'hasPhoneNumber' => false,
            'times' => []
        ]);
    }
}
