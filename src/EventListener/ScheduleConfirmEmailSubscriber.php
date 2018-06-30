<?php

namespace App\EventListener;

use App\Entity\Schedule;
use App\Entity\User;
use App\Event\ScheduleEvents;
use App\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ScheduleConfirmEmailSubscriber implements EventSubscriberInterface
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var \Twig_Environment
     */
    private $twig;
    private $emailFromAddress;
    private $siteTitle;
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * ScheduleCreateSubscriber constructor.
     * @param \Swift_Mailer $mailer
     * @param \Twig_Environment $twig
     * @param UserRepository $userRepository
     * @param $emailFromAddress
     * @param $siteTitle
     */
    public function __construct(
        \Swift_Mailer $mailer,
        \Twig_Environment $twig,
        UserRepository $userRepository,
        $emailFromAddress,
        $siteTitle
    )
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->emailFromAddress = $emailFromAddress;
        $this->siteTitle = $siteTitle;
        $this->userRepository = $userRepository;
    }

    /**
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            ScheduleEvents::SCHEDULE_UPDATE_CONFIRM => 'onUpdateConfirm'
        ];
    }

    /**
     * @param GenericEvent $event
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function onUpdateConfirm(GenericEvent $event)
    {
        /** @var Schedule $schedule */
        $schedule = $event->getSubject();

        /********* CUSTOMER EMAIL **********/
        $message = (new \Swift_Message())
            ->setSubject('Appointment updated')
            ->setFrom([$this->emailFromAddress => $this->siteTitle])
            ->setTo($schedule->getCustomer()->getEmail())
            ->setBody(
                $this->twig->render('site/schedule/emailUpdateConfirm.html.twig', [
                    'schedule' => $schedule,
                ]),
                'text/html'
            );

        $this->mailer->send($message);
    }
}