<?php

namespace App\EventListener;

use App\Entity\Schedule;
use App\Entity\User;
use App\Event\ScheduleEvents;
use App\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ScheduleCreateSubscriber implements EventSubscriberInterface
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
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * ScheduleCreateSubscriber constructor.
     * @param \Swift_Mailer $mailer
     * @param \Twig_Environment $twig
     * @param TokenStorageInterface $tokenStorage
     * @param UserRepository $userRepository
     * @param $emailFromAddress
     * @param $siteTitle
     */
    public function __construct(
        \Swift_Mailer $mailer,
        \Twig_Environment $twig,
        TokenStorageInterface $tokenStorage,
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
        $this->tokenStorage = $tokenStorage;
    }

    /**
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            ScheduleEvents::SCHEDULE_CREATE_COMPLETED => 'onCreateCompleted'
        ];
    }

    /**
     * @param GenericEvent $event
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function onCreateCompleted(GenericEvent $event)
    {
        /** @var Schedule $schedule */
        $schedule = $event->getSubject();

        /********* USER ADMIN EMAILS **********/

        /** @var User[] $users */
        $users = $this->userRepository->queryLatestForm()->getQuery()->getResult();

        $emailsUserAdmin = [];
        foreach ($users as $user) {
            if ($user->getReceiveEmails()) {
                $emailsUserAdmin[] = $user->getEmail();
            }
        }

        /*$message = (new \Swift_Message())
            ->setSubject('Novo schedule recebido')
            ->setFrom([$this->emailFromAddress => $this->siteTitle])
            ->setTo($emailsUserAdmin)
            ->setBody(
                $this->twig->render('site/schedule/emailAdmin.html.twig', [
                    'schedule' => $schedule,
                    'user' => $this->tokenStorage->getToken()->getUser()
                ]),
                'text/html'
            );

        $this->mailer->send($message);*/


        /********* CUSTOMER EMAIL **********/

    }
}