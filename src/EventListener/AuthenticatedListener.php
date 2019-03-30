<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTAuthenticatedEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class AuthenticatedListener
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }
    
    /**
     * @param JWTAuthenticatedEvent $event
     */
    public function onJwtAuthenticatedRequest(JWTAuthenticatedEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();

        $user = $event->getPayload();
        $userEmail = $user['email'];
        
        $request->attributes->set('userEmail', $userEmail);
    }
}
