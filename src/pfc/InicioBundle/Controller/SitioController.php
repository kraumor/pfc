<?php

namespace pfc\InicioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use pfc\InicioBundle\Entity\Contacto;
use pfc\InicioBundle\Form\ContactoType;
//use Symfony\Component\HttpFoundation\Response;

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
    
    public function contactoAction()
    {        
        $contacto = new Contacto();
        $form = $this->createForm(new ContactoType(), $contacto);
        //$form = $this->get('form.factory')->create(new ContactoType(),array());

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            
            if ($form->isValid()) {
                // enviar el email
                $message = \Swift_Message::newInstance()
                    ->setSubject('Solicitud de contacto desde PFC')
                    ->setFrom('contacto@pfc.com')
                  //->setTo('contacto@email.com')
                  //->setTo($contacto->getEmail())
                    ->setTo($this->container->getParameter('pfc_inicio.emails.contacto'))
                    ->setBody($this->renderView('InicioBundle:Sitio:contactoEmail.txt.twig', array('contacto' => $contacto)));
                $this->get('mailer')->send($message);

                $this->get('session')->setFlash('contacto-aviso', 'Mensaje enviado correctamente.');

                // Redirect - This is important to prevent users re-posting
                // the form if they refresh the page
                return $this->redirect($this->generateUrl('contacto'));                  
            }
        }
        return $this->render('InicioBundle:Sitio:contacto.html.twig', array('form' => $form->createView()));
    }        
}