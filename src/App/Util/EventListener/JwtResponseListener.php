<?php

namespace App\Util\EventListener;

use App\Util\Controller\EntitySerializableTrait as EntitySerializable;
use Doctrine\ORM\EntityManager;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

/**
 * JWT Response listener.
 *
 * @author Robin Chalas <rchalas@sutunam.com>
 */
class JwtResponseListener
{
    use EntitySerializable;

    /**
     * Constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * Add public data to the authentication response.
     *
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $username = $event->getUser() ? $event->getUser()->getUsername() : '';
        $userManager = $this->em->getRepository('AppUserBundle:User');
        $user = $userManager->findOneBy(['username' => $username]);

        $data['user'] = json_decode(
            $this->serialize($user, array('groups' => ['api']))
        );

        if ('guest@sportroops.fr' == $username) {
            unset($data['user']);
        }


        $event->setData($data);
    }
}
