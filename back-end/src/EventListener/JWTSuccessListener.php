<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;

class JWTSuccessListener
{
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event): void
    {
        $data = $event->getData();
        $user = $event->getUser();
        if (!$user instanceof UserInterface) {
            return;
        }

        $data['user'] = [
            'id' => method_exists($user, 'getId') ? $user->getId() : null,
            'email' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
        ];

        $event->setData($data);
    }
}