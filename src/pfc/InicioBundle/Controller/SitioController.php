<?php

namespace pfc\InicioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SitioController extends Controller
{
    /**
     * Muestra las páginas estáticas del sitio web
     *
     * @param string $pagina El slug de la página a mostrar
     */
    public function estaticaAction($pagina)
    {
        $plantilla = realpath(__DIR__.'/../Resources/views/Sitio/'.$pagina.'.html.twig');
        
        if(file_exists($plantilla)){
            $respuesta = $this->render('InicioBundle:Sitio:'.$pagina.'.html.twig');

            //$respuesta->setSharedMaxAge(3600 * 24);
            //$respuesta->setPublic();

            return $respuesta;
        }
        else{
            throw $this->createNotFoundException('No se ha encontrado la página solicitada');
        }
    }
}