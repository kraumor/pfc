<?php

namespace pfc\InicioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use pfc\InicioBundle\Entity\Usuario;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        $x=new \DateTime;
        $x=new \DateTime('2000-01-01 12:12:12');
        $x->modify('+2 months');
        
//        $user1=$this->get('security.context')->getToken()->getUser();
//        $user1=new Usuario();        
//        $data1=  is_null($user1->getFechaAlta())? 1:2;
//        $data2=  is_null($user1->getFechaBaja())? 1:2;
        
        
        $vector = array (  'gravatarurl'  => 'http://www.gravatar.com/avatar/'
                          ,'gravatarhash' => md5(strtolower(trim("contacto@email.com ")))
                          ,'data'         => $x->format('l, Y-F-d H:i:s') 
//                          ,'user1'        => $user1
//                          ,'request'      => print_r($this->getRequest(),true)
//                          ,'data1'        => $data1
//                          ,'data2'        => $data2
                          ,'fecha'        => date('Y-m-d H:i:s',1364242101)
                        );

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
