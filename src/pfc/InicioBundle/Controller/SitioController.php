<?php

namespace pfc\InicioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use pfc\InicioBundle\Entity\Contacto;
use pfc\InicioBundle\Entity\Usuario;
use pfc\InicioBundle\Form\ContactoType;
use pfc\InicioBundle\Form\UsuarioType;
use pfc\InicioBundle\Form\UsuarioModificarType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class SitioController extends Controller
{
    private function getAvatar($size=80){
        
        //https://es.gravatar.com/site/implement/images/
        
        $default_avatar=urldecode($this->getRequest()->getUriForPath($this->container->getParameter('pfc_inicio.default_avatar')));
        
        $d_keywords=array(
             '0' =>  $default_avatar
            ,'1' =>  '404'          // do not load any image if none is associated with the email hash, instead return an HTTP 404 (File Not Found) response
            ,'2' =>  'mm'           // (mystery-man) a simple, cartoon-style silhouetted outline of a person (does not vary by email hash)
            ,'3' =>  'identicon'    // a geometric pattern based on an email hash
            ,'4' =>  'monsterid'    // a generated 'monster' with different colors, faces, etc
            ,'5' =>  'wavatar'      // generated faces with differing features and backgrounds
            ,'6' =>  'retro'        // awesome generated, 8-bit arcade-style pixelated faces
            ,'7' =>  'blank'        // a transparent PNG image (border added to HTML below for demonstration purposes)
        );
        
        $avatar=null;
        
        if($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')){
            
            //$usuario = $this->get('security.context')->getToken()->getUser();
            $usuario = $this->getUser();

            $gravatarUrl = $this->container->getParameter('pfc_inicio.gravatarUrl').'/avatar/';
            $gravatarHash = md5(strtolower(trim($usuario->getEmail())));    //$gravatarHash = '205e460b479e2e5b48aec07710c08d50'; 
            $parametros='?';
            //pendi: comprobar q con 0 se vea la imagen por defecto
            $parametros.='d='.$d_keywords['3'];
            $parametros.=($size==80)    ?   null    :   '&s='.$size ;
            $avatar=$gravatarUrl.$gravatarHash.$parametros;
        }
        
        return $avatar;        
    }

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
              //$this->get('session')->getFlashBag()->add('contacto-aviso', 'Mensaje enviado correctamente.');

                // Redirect - This is important to prevent users re-posting
                // the form if they refresh the page
                return $this->redirect($this->generateUrl('contacto'));                  
            }
        }
        return $this->render('InicioBundle:Sitio:contacto.html.twig', array(
            'form' => $form->createView()));
    }
    
    public function perfilAction()
    {        
        //TODO: poder dar de baja la cuenta, set fechaBaja
        //TODO: poder modificar contraseña, pedir la anterior y 2 veces la nueva
        //http://symfony.com/doc/current/reference/constraints/UserPassword.html
        //http://showmethecode.es/php/symfony/userpassword-constraint/
        //http://sf2.showmethecode.es/app.php/ejemplo/userpassword
        
        $usuario = $this->getUser();
        
        $formulario = $this->createForm(new UsuarioModificarType(), $usuario);
        
        $peticion = $this->getRequest();

            //peticion POST: actualizar
            if ($peticion->getMethod() == 'POST') {
                
                $emailOriginal = $formulario->getData()->getEmail();
                $passwordOriginal = $formulario->getData()->getPassword();
                
                $formulario->bind($peticion);
                
//                $usuario->setEmail($emailOriginal);
//                $usuario->setPassword($passwordOriginal);
//                $usuario->setApellidos('++');
//                $usuario->setFechaBaja(new \Datetime());
//                $usuario->setViajeId(1);

                if ($formulario->isValid()) {
//                    if (null == $usuario->getPassword()) {
//                    $usuario->setPassword($passwordOriginal);
//                    }
//                    else {
//                        $encoder = $this->get('security.encoder_factory')->getEncoder($usuario);
//                        $passwordCodificado = $encoder->encodePassword(
//                            $usuario->getPassword(),
//                            $usuario->getSalt()
//                        );
//                        $usuario->setPassword($passwordCodificado);
//                    }
//                    $encoder = $this->get('security.encoder_factory')->getEncoder($usuario);
//                    $passwordCodificado = $encoder->encodePassword($usuario->getPassword(),$usuario->getSalt());
//                    $usuario->setPassword($passwordCodificado);                    
//
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($usuario);
                    $em->flush();
                    
                    $flashBag = $this->get('session')->getFlashBag();
                    $flashBag->add('info', 'OK');

                    return $this->redirect($this->generateUrl('usuario_perfil'));
                
//
//                    $this->get('session')->setFlash('info','Los datos de tu perfil se han actualizado correctamente');
//                    return $this->redirect($this->generateUrl('usuario_perfil'));
//                return new Response('OK<pre>'.$formulario->count().$emailOriginal.print_r($usuario,true).'</pre>');
                }
                $flashBag = $this->get('session')->getFlashBag();
                $flashBag->add('error', 'KO');

                return $this->redirect($this->generateUrl('usuario_perfil'));
//                return new Response('KO<pre>'.$formulario->count().$emailOriginal.print_r($usuario,true).'</pre>');
            }
            //peticion GET: mostrar
            return $this->render('InicioBundle:Sitio:perfil.html.twig', array(
                     'avatar'     => $this->getAvatar()
                    ,'gravatarUrl'=> $this->container->getParameter('pfc_inicio.gravatarUrl')
                    ,'usuario'    => $usuario
                    ,'formulario' => $formulario->createView()
            ));
    }
    
    public function perfilmAction()
    {        
        $usuario = $this->getUser();
        
        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository('InicioBundle:Usuario')->find($usuario);
        
        return $this->render('InicioBundle:Sitio:perfilm.html.twig', array(
             'usuario'  => $usuario 
            ,'avatar'   => $this->getAvatar(50) ));
    }
    
    public function loginAction()
    {    
        $request = $this->getRequest();
        $session = $request->getSession();
 
        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }
 
        return $this->render('InicioBundle:Sitio:login.html.twig', array(
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error ));
    }    
    
    public function cajaLoginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
 
        $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR
                            ,$session->get(SecurityContext::AUTHENTICATION_ERROR) );
 
        return $this->render('InicioBundle:Sitio:cajaLogin.html.twig', array(
             'last_username' => $session->get(SecurityContext::LAST_USERNAME)
            ,'error'         => $error 
            ,'avatar'        => $this->getAvatar(20)
            ));
    }    
    
    public function registroAction()
    {
        $peticion = $this->getRequest();
        
        $usuario = new Usuario();
        $formulario = $this->createForm(new UsuarioType(), $usuario);

        if ($peticion->getMethod() == 'POST') {
            
            $formulario->bind($peticion);

            if ($formulario->isValid()) {
                $encoder = $this->get('security.encoder_factory')->getEncoder($usuario);
                $usuario->setSalt();
                $passwordCodificado = $encoder->encodePassword($usuario->getPassword(),$usuario->getSalt());
                $usuario->setPassword($passwordCodificado);

                $em = $this->getDoctrine()->getManager();
                $em->persist($usuario);
                $em->flush();
                
                //mensaje flash
                $this->get('session')->setFlash('info','¡Enhorabuena! Te has registrado correctamente en PFC');

                //loguear al nuevo usuario
                $token = new UsernamePasswordToken(
                        $usuario,
                        $usuario->getPassword(),
                        'chain_provider',
                        $usuario->getRoles()
                        );
                 $this->container->get('security.context')->setToken($token);
              
                return $this->redirect($this->generateUrl('portada'));
            }
        }
        
        return $this->render('InicioBundle:Sitio:registro.html.twig'
                        ,array('formulario' => $formulario->createView()));
    }    
}