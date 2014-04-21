<?php

namespace pfc\InicioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use pfc\InicioBundle\Entity\Usuario;
use Symfony\Component\Finder\Finder;

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
    

    /**
     * Muestra el avatar (Gravatar) del usuario autenticado
     * usar gravatarbundle en vez de esta funcion
     */
    public function getAvatar($size=80) {
        //https://es.gravatar.com/site/implement/images/

        $default_avatar=urldecode($this->getRequest()->getUriForPath($this->container->getParameter('pfc_inicio.default_avatar')));

        $d_keywords=array(
            '0' => $default_avatar
            ,'1' => '404'          // do not load any image if none is associated with the email hash, instead return an HTTP 404 (File Not Found) response
            ,'2' => 'mm'           // (mystery-man) a simple, cartoon-style silhouetted outline of a person (does not vary by email hash)
            ,'3' => 'identicon'    // a geometric pattern based on an email hash
            ,'4' => 'monsterid'    // a generated 'monster' with different colors, faces, etc
            ,'5' => 'wavatar'      // generated faces with differing features and backgrounds
            ,'6' => 'retro'        // awesome generated, 8-bit arcade-style pixelated faces
            ,'7' => 'blank'        // a transparent PNG image (border added to HTML below for demonstration purposes)
        );

        $avatar=null;

        if($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')){

            //$usuario = $this->get('security.context')->getToken()->getUser();
            $usuario=$this->getUser();

            $gravatarUrl=$this->container->getParameter('pfc_inicio.gravatarUrl').'/avatar/';
            $gravatarHash=md5(strtolower(trim($usuario->getEmail())));    //$gravatarHash = '205e460b479e2e5b48aec07710c08d50'; 
            $parametros='?';
            //pendi: comprobar q con 0 se vea la imagen por defecto
            $parametros.='d='.$d_keywords['3'];
            $parametros.=($size==80) ? null : '&s='.$size;
            $avatar=$gravatarUrl.$gravatarHash.$parametros;
        }
        return $avatar;
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
        
//        $finder = 'new Finder()'
//        $finder->files()->in(__DIR__.'../../');
$finder=array(1,2,3,4);

        $respuesta = $this->render('InicioBundle:Default:x1.html.twig',array(
                'x' => 'adios'
                ,'finder' => $finder
            
            ));

        return $respuesta;
    }
    
    public function viajeOOAction() {

        $em=$this->getDoctrine()->getManager();

//return new response($usuario);
//        $viajes=$em->getRepository('InicioBundle:Usuario')->find($usuario->getId());
//        $viajes=$em->getRepository('InicioBundle:Usuario')->findViajesRecientes($usuario->getId());
        $viajes=$em->getRepository('InicioBundle:Viaje')->findAll();

        return $this->render('InicioBundle:Default:viajeOO.html.twig',array(
                    'viajes' => $viajes
        ));
    }
}
