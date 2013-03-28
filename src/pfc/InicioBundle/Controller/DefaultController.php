<?php

namespace pfc\InicioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use pfc\InicioBundle\Entity\Usuario;

class DefaultController extends Controller {

    public function indexAction($pag) {

        $plantilla=realpath(__DIR__.'/../Resources/views/Default/'.$pag.'.html.twig');

        if(file_exists($plantilla)){

            switch ($pag){
                case "x0":
//                    $codigo=$this->getRequest()->getUri();                // http://localhost/pfc/web/app_dev.php/sitio/activar?codigo=asd%25q%2Fwe
//                    $codigo=$this->getRequest()->getBasePath();           // /pfc/web
//                    $codigo=$this->getRequest()->getBaseUrl();            // /pfc/web/app_dev.php
//                    $codigo=$this->getRequest()->getClientIp();           // 127.0.0.1
//                    $codigo=$this->getRequest()->getHost();               // localhost
//                    $codigo=$this->getRequest()->getPathInfo();           // /sitio/activar
//                    $codigo=$this->getRequest()->getQueryString();        // codigo=asd%25q%2Fwe
//                    $codigo=$this->getRequest()->getRequestUri();         // /pfc/web/app_dev.php/sitio/activar?codigo=asd%q/we
//                    $codigo=$this->getRequest()->getSchemeAndHttpHost();  // http://localhost         

                    $respuesta=$this->forward('InicioBundle:Default:x0',array('name' => $pag));
                    break;
                case "x1":
                    $respuesta=$this->forward('InicioBundle:Default:x1');
                    break;
                default:
                    $respuesta=$this->render('InicioBundle:Default:'.$pag.'.html.twig');
            }
            return $respuesta;
        }
        else{
            throw $this->createNotFoundException('No se ha encontrado la página solicitada');
        }
    }

    public function x0Action($name) {
        $x=new \DateTime;
        $x=new \DateTime('2000-01-01 12:12:12');
        $x->modify('+2 months');

//        $user1=$this->get('security.context')->getToken()->getUser();
//        $user1=new Usuario();        
//        $data1=  is_null($user1->getFechaAlta())? 1:2;
//        $data2=  is_null($user1->getFechaBaja())? 1:2;


        $vector=array('gravatarurl' => 'http://www.gravatar.com/avatar/'
            ,'gravatarhash' => md5(strtolower(trim("contacto@email.com ")))
            ,'data' => $x->format('l, Y-F-d H:i:s')
//                          ,'user1'        => $user1
//                          ,'request'      => print_r($this->getRequest(),true)
//                          ,'data1'        => $data1
//                          ,'data2'        => $data2
            ,'fecha' => date('Y-m-d H:i:s',1364242101)
        );

        //$respuesta = new Response('Texto muestra.');
        //$respuesta = $this->render('::base.html.twig');
        //$respuesta = $this->render('::frontend.html.twig');
        $respuesta=$this->render('InicioBundle:Default:x0.html.twig',array('name' => $name,'vector' => $vector));

        return $respuesta;
    }

    public function x1Action() {


        $respuesta = $this->render('InicioBundle:Default:x1.html.twig',array('x' => 'adios'));

        return $respuesta;
    }
}
