<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\Customer;
use App\Entity\CustomerAddresses;
use App\Entity\Schedule;
use App\Form\Model\DatePickerType;
use App\Repository\CustomerRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

class ScheduleSiteType extends AbstractType
{
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    /**
     * ScheduleSiteType constructor.
     * @param SessionInterface $session
     * @param CustomerRepository $customerRepository
     */
    public function __construct(SessionInterface $session, CustomerRepository $customerRepository)
    {
        $this->session = $session;
        $this->customerRepository = $customerRepository;
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

        if (!$options['hasCustomer']) {
            $builder->add('customer', CustomerType::class, [
                'label' => 'customer.title_single',
                'constraints' => [
                    new Valid()
                ]
            ]);
        }

        $formModifier = function (FormInterface $form) use ($options) {
            /** @var \DateTime $date */
            $date = $form->get('date')->getData();
            $day = $date ? $date->format('N') : null;

            $timeOptions = $day && $day == 6 ? $options['times']['weekend']['options'] : $options['times']['week']['options'];

            $timeChoices = [];
            foreach ($timeOptions as $time) {
                $timeChoices[] = $time['time_start'] . '__' . $time['time_end'];
            }

            $form->add('times', ChoiceType::class, array(
                'mapped' => false,
                'expanded' => true,
                'multiple' => false,
                'choices' => $timeChoices,
                'constraints' => [
                    new NotBlank()
                ]
            ));
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($formModifier, $options) {

            $formModifier($event->getForm());

            /** @var Schedule $schedule */
            $schedule = $event->getData();

            if ($options['hasCustomer']) {
                /** @var Customer $customer */
                $customer = $this->customerRepository->findOneBy([
                    'email' => $this->session->get('email')
                ]);
                $schedule->setCustomer($customer);
            }

            if (!$options['hasCustomer']) {
                $email = $this->session->has('newEmail') ? $this->session->get('newEmail') : '';
                $schedule->setCustomer(
                    (new Customer())
                        ->setEmail($email)
                        ->addCustomerAddress(
                            (new CustomerAddresses())
                                ->setAddress(
                                    (new Address())
                                        ->setZipCode($this->session->get('zipCode'))
                                        ->setCity($this->session->get('city'))
                                        ->setState('UT')
                                )
                        )
                );
            }
        });

        $builder->get('date')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $formModifier($event->getForm()->getParent());
            }
        );

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

            if (!$options['hasCustomer']) {
                $schedule['customer']['customerAddresses'][0]['address']['zipCode'] = $this->session->get('zipCode');
                $schedule['customer']['customerAddresses'][0]['address']['city'] = $this->session->get('city');
                $schedule['customer']['customerAddresses'][0]['address']['state'] = 'UT';
            }

            $event->setData($schedule);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Schedule::class,
            'hasCustomer' => $this->session->has('email'),
            'times' => []
        ]);
    }
}
