<?php

namespace pfc\InicioBundle\Listener;
 
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
 
class LoginListener
{
    /**
     * @var Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $doctrine;
 
    public function __construct(Doctrine $doctrine)
    {
        $this->doctrine = $doctrine;
    }
  
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        //http://www.ens.ro/2012/03/14/symfony2-login-event-listener/
        //http://dev.dbl-a.com/symfony-2-0/how-to-add-a-symfony2-login-event-listener/
        //http://www.metod.si/login-event-listener-in-symfony2/
        //http://zechim.com/blog/2013/01/15/event-listener-on-symfony-2-login/

        $usuario = $event->getAuthenticationToken()->getUser();
        if($usuario)
        {
            $usuario->nuevaConexion(new \DateTime());

            $em = $this->doctrine->getManager();
            $em->persist($usuario);
            $em->flush();            
        }                  
    }
}
