<?php

namespace pfc\InicioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        //$respuesta = new Response('Texto muestra.');
        //$respuesta = $this->render('::base.html.twig');
        //$respuesta = $this->render('::frontend.html.twig');
        $respuesta = $this->render('InicioBundle:Default:index.html.twig', array('name' => $name));
        
        return $respuesta;       
    }
    
    public function portadaAction()
    {
        return $this->render('InicioBundle:Default:portada.html.twig');
    }
}
