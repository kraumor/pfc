<?php

namespace pfc\InicioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        $x=new \DateTime;
        $x=new \DateTime('2000-01-01 12:12:12');
        $x->modify('+2 months');
        $vector = array (  'gravatarurl'  => 'http://www.gravatar.com/avatar/'
                          ,'gravatarhash' => md5(strtolower(trim("contacto@email.com ")))
                          ,'data'         => $x->format('l, Y-F-d H:i:s') );

        //$respuesta = new Response('Texto muestra.');
        //$respuesta = $this->render('::base.html.twig');
        //$respuesta = $this->render('::frontend.html.twig');
        $respuesta = $this->render('InicioBundle:Default:index.html.twig', array('name' => $name,'vector' => $vector));
        
        return $respuesta;       
    }
    
    public function portadaAction()
    {
        return $this->render('InicioBundle:Default:portada.html.twig');
    }
}
