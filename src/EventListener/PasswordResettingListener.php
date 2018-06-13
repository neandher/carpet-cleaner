<?php

namespace App\EventListener;

use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PasswordResettingListener implements EventSubscriberInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * PasswordResettingListener constructor.
     * @param UrlGeneratorInterface $router
     * @param SessionInterface $session
     */
    public function __construct(UrlGeneratorInterface $router, SessionInterface $session)
    {
        $this->router = $router;
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::RESETTING_SEND_EMAIL_INITIALIZE => 'onResettingResetInitialize',
            FOSUserEvents::RESETTING_RESET_SUCCESS => 'onPasswordResettingSuccess',
        );
    }

    /**
     * @param GetResponseUserEvent $event
     */
    public function onResettingResetInitialize(GetResponseUserEvent $event)
    {
        if (!$event->getUser()) {
            $this->session->getFlashBag()->add('danger', 'User not found');
            $event->setResponse(new RedirectResponse($this->router->generate('fos_user_resetting_request')));
        }
    }

    public function onPasswordResettingSuccess(FormEvent $event)
    {
        $url = $this->router->generate('admin_dashboard');

        $event->setResponse(new RedirectResponse($url));
    }
}