<?php


namespace App\EventListener;

use App\Entity\UserPlateform;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTAuthenticatedEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;

class JWTListener extends AbstractController
{

    private $security;


    /**
     * JWTCreatedListener constructor.
     */
    public function __construct()
    {
    }

    public function onJWTCreated(JWTCreatedEvent $event)
    {
        /** @var UserPlateform $user */
        $user = $event->getUser();
        $payload = $event->getData();
        $payload['id'] = $user->getId();
        $payload['nom'] = $user->getNom();
        $payload['prenom'] =   $user->getPrenom();
        $payload['email'] = $user->getEmail();
        $payload['keySecret'] = $user->getKeySecret();

        $event->setData($payload);
    }

    public function onJWTAuthenticated(JWTAuthenticatedEvent $event)
    {
        $token = $event->getToken();
        $payload = $event->getPayload();
        $token->setAttribute('id', $payload['id']);
        $token->setAttribute('nom', $payload['nom']);
        $token->setAttribute('prenom', $payload['prenom']);
        $token->setAttribute('keySecret', $payload['keySecret']);
        $token->setAttribute('email', $payload['email']);
    }

    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        $event->setData($data);
    }
}
