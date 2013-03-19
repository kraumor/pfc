<?php

namespace pfc\InicioBundle\Listener;
 
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
//use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
//use Symfony\Component\HttpFoundation\RedirectResponse;
//use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\Routing\Router;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
 
class LoginListener
{
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $usuario = $event->getAuthenticationToken()->getUser();
        if($usuario)
        {
          $usuario->nuevaConexion(new \DateTime());
        }                  
        
    }
    
    public function onKernelResponse(FilterResponseEvent $event)
    {
//        $portada = 'contacto';
//        $event->setResponse(new RedirectResponse($portada));
    }    
}
