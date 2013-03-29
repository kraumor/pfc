<?php

namespace pfc\InicioBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ExtranetController extends Controller {

    // ...

    public function portadaAction() {

        $em=$this->getDoctrine()->getManager();
        $usuario=$this->get('security.context')->getToken()->getUser();
        
//return new response($usuario);
//        $viajes=$em->getRepository('InicioBundle:Usuario')->find($usuario->getId());
        $viajes=$em->getRepository('InicioBundle:Usuario')->findViajesRecientes($usuario->getId());

        return $this->render('InicioBundle:Extranet:portada.html.twig',array(
                    'viajes' => $viajes
        ));
    }

}