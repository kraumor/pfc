<?php

namespace pfc\InicioBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use pfc\InicioBundle\Entity\Contacto;
use pfc\InicioBundle\Entity\Usuario;
use pfc\InicioBundle\Form\ContactoType;
use pfc\InicioBundle\Form\UsuarioType;
use pfc\InicioBundle\Form\UsuarioModificarType;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class SitioController extends Controller {

    public function portadaAction() {
        return $this->render('InicioBundle:Sitio:portada.html.twig');
    }

    /**
     * Muestra las páginas estáticas del sitio web
     */
    public function estaticaAction($pagina) {

        $plantilla=realpath(__DIR__.'/../Resources/views/Sitio/'.$pagina.'.html.twig');

        if(file_exists($plantilla)){

            switch ($pagina){
                case "activar":
                    $codigo=$this->getRequest()->getQueryString();
                    $respuesta=$this->forward('InicioBundle:Sitio:activarCuenta',array('codigo' => $codigo));
                    break;
                default:
                    $respuesta=$this->render('InicioBundle:Sitio:'.$pagina.'.html.twig');
            }
            return $respuesta;
        }
        else{
            throw $this->createNotFoundException('No se ha encontrado la página solicitada');
        }
    }

    /**
     * Activa la cuenta recien registrada mediante el código generado al finalizar el registro
     */
    public function activarCuentaAction($codigo=null) {

        $defaultData=array('codigo' => $codigo);
        $form=$this->createFormBuilder($defaultData)
                ->add('codigo','textarea')
                ->getForm();

        $request=$this->getRequest();

        if($request->getMethod()=='POST'){
            $form->bind($request);
            if($form->isValid()){
                $datos=$form->getData('codigo');
                $codigo=$datos['codigo'];
            }
            else{
                throw $this->createNotFoundException('Formulario no válido.');
            }
        }

        if($codigo){

            //desencripta cadena    
            $codigo=urldecode(trim($codigo));
            $codigo=base64_decode($codigo);
            $x2=$codigo;
            $clave=substr($codigo,0,2);
            $datos=substr($codigo,2);
            $datos=$this->desencriptar($datos,$clave);

            //valida los datos
            if(count(explode('·',$datos))==3){
                list($a,$b,$c)=explode('·',$datos);
                if(!is_numeric($a)||!date('Y-m-d H:i:s',$b)){
                    throw $this->createNotFoundException('Código no válido.');
                }
            }
            else{
                throw $this->createNotFoundException('Código inválido.');
            }
            $formato='Y-m-d H:i:s';
            $b1=date($formato,$b);                              // string
            $b2=\DateTime::createFromFormat($formato,$b1);      // \Datetime
            //busca usuario en BBDD
            $em=$this->getDoctrine()->getManager();
            //$usuario = $em->getRepository('InicioBundle:Usuario')->findUsuarioRegistrado($a,$b2,$c);
            $usuario=$em->getRepository('InicioBundle:Usuario')->findOneBy(array('id' => $a,'fechaBaja' => $b2,'password' => $c));
            //$arr=array($a,$b2,$c);
            //return new Response('REPOSITORY<pre>'.print_r($usuario, true) . '</pre>');
            //activa la cuenta borrando fechaBaja
            if($usuario){
                $usuario->setFechaBaja(null);
                $em->persist($usuario);
                $em->flush();

                //loguea al usuario
                $token=new UsernamePasswordToken($usuario,$usuario->getPassword(),'chain_provider',$usuario->getRoles());
                $this->container->get('security.context')->setToken($token);

                $respuesta=$this->redirect($this->generateUrl('portada'));
            }
            else{
                throw $this->createNotFoundException('Código ya usado.');
            }
        }
        else{
            $respuesta=$this->render('InicioBundle:Sitio:activar.html.twig',array(
                'codigo' => $codigo
                ,'form' => $form->createView()
            ));
        }
        return $respuesta;
    }

    public function contactoAction() {

        $contacto=new Contacto();
        $form=$this->createForm(new ContactoType(),$contacto);

        $request=$this->getRequest();
        if($request->getMethod()=='POST'){

            $form->bindRequest($request);

            if($form->isValid()){

                $FromEmail=$this->container->getParameter('pfc_inicio.emails.contacto');
                $FromNombre=$this->container->getParameter('pfc_inicio.proyecto');
                $ToEmail=$form->getData()->getEmail();
                $ToNombre=$form->getData()->getNombre();

                // enviar el email
                $message=\Swift_Message::newInstance()
                        ->setSubject('Solicitud de contacto desde PFC')
                        ->setFrom(array($FromEmail => $FromNombre))
                        ->setTo(array($ToEmail => $ToNombre))
                        ->setBody($this->renderView('InicioBundle:Sitio:contactoEmail.txt.twig',array('contacto' => $contacto)));
                $this->get('mailer')->send($message);

                $this->get('session')->getFlashBag()->add('contacto-aviso','Mensaje enviado correctamente.');

                // Redirect - This is important to prevent users re-posting the form if they refresh the page
                return $this->redirect($this->generateUrl('contacto'));
            }
        }
        return $this->render('InicioBundle:Sitio:contacto.html.twig',array(
                    'form' => $form->createView()));
    }

    /**
     * Perfil usuario: muestra valores, permite modificarlos y dar de baja la cuenta
     */
    public function perfilAction() {
        //TODO: poder modificar contraseña, pedir la anterior y 2 veces la nueva
        //http://symfony.com/doc/current/reference/constraints/UserPassword.html
        //http://showmethecode.es/php/symfony/userpassword-constraint/
        //http://sf2.showmethecode.es/app.php/ejemplo/userpassword

        $usuario=$this->getUser();

        $formulario=$this->createForm(new UsuarioModificarType(),$usuario);

        $peticion=$this->getRequest();

        //peticion POST: recibe formulario
        if($peticion->getMethod()=='POST'){

            //comprobar operacion correcta
            $desactivar=$peticion->request->get('desactivar_cuenta',false);
            $operacion=$peticion->request->get('operacion','0+0');
            $resultado_respondido=$peticion->request->get('resultado',0);
            list($num1,$num2)=explode('+',$operacion);
            $resultado_real=$num1+$num2;
            $operacion_ok=($resultado_real==$resultado_respondido);

            if($desactivar&&$operacion_ok){

                $usuario->setFechaBaja(new \DateTime);
                $em=$this->getDoctrine()->getManager();
                $em->persist($usuario);
                $em->flush();

                return $this->redirect($this->generateUrl('usuario_logout'));
            }
            else{
                $passwordOriginal=$formulario->getData()->getPassword();

                $formulario->bind($peticion);
                
                //pendi:error si contraseñas difieren salta error operacion
                if($formulario->isValid()){
                    if (null == $usuario->getPassword()) {
                        $usuario->setPassword($passwordOriginal);
                    }
                    else {
                        $encoder = $this->get('security.encoder_factory')->getEncoder($usuario);
                        $passwordCodificado = $encoder->encodePassword(
                            $usuario->getPassword(),
                            $usuario->getSalt()
                        );
                        $usuario->setPassword($passwordCodificado);
                    }
                    $em=$this->getDoctrine()->getManager();
                    $em->persist($usuario);
                    $em->flush();

                    $this->get('session')->getFlashBag()->add('info1','Datos modificados correctamente.');

                    return $this->redirect($this->generateUrl('usuario_perfil'));
                }
                $this->get('session')->getFlashBag()->add('info2','Resultado de la operación incorrecto, no se desactiva la cuenta.');

                return $this->redirect($this->generateUrl('usuario_perfil'));
            }
        }
        //peticion GET: mostrar
        return $this->render('InicioBundle:Sitio:perfil.html.twig',array(
                    'avatar' => $this->getAvatar()
                    ,'gravatarUrl' => $this->container->getParameter('pfc_inicio.gravatarUrl')
                    ,'usuario' => $usuario
                    ,'formulario' => $formulario->createView()
                    ,'operacion' => rand(9,990).'+'.rand(1,9)
        ));
    }

    public function loginAction() {
        $request=$this->getRequest();
        $session=$request->getSession();

        // get the login error if there is one
        if($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)){
            $error=$request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        }
        else{
            $error=$session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render('InicioBundle:Sitio:login.html.twig',array(
                    'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                    'error' => $error));
    }

    public function cajaLoginAction() {
        $request=$this->getRequest();
        $session=$request->getSession();

        $error=$request->attributes->get(SecurityContext::AUTHENTICATION_ERROR
                ,$session->get(SecurityContext::AUTHENTICATION_ERROR));

        return $this->render('InicioBundle:Sitio:cajaLogin.html.twig',array(
                    'last_username' => $session->get(SecurityContext::LAST_USERNAME)
                    ,'error' => $error
                    ,'avatar' => $this->getAvatar(20)
        ));
    }
    
    public function cajaAction() {
        $request=$this->getRequest();
        $session=$request->getSession();

        $error=$request->attributes->get(SecurityContext::AUTHENTICATION_ERROR
                ,$session->get(SecurityContext::AUTHENTICATION_ERROR));

        return $this->render('InicioBundle:Sitio:caja.html.twig',array(
                    'last_username' => $session->get(SecurityContext::LAST_USERNAME)
                    ,'error' => $error
                    ,'avatar' => $this->getAvatar(35)
        ));
    }

    /**
     * Registra una cuenta y establece su fechaBaja == fechaAlta como indicativo de que está pendiente de confirmar.
     * Como tiene fechaBaja, se comporta como una cuenta desactivada y por lo tanto no puede loguear.
     * Se genera un código de activación encriptando (id,fechaBaja,password) que se envia al email de registro.
     * Recibir el código verifica implícitamente el email, y usándolo en activar_cuenta borra la fechaBaja, dejando la cuenta habilitada.
     */
    public function registroAction($codigo) {
        $peticion=$this->getRequest();

        $usuario=new Usuario();
        $formulario=$this->createForm(new UsuarioType(),$usuario);

        if($peticion->getMethod()=='POST'){

            $formulario->bind($peticion);

            if($formulario->isValid()){

                //codifica password
                $encoder=$this->get('security.encoder_factory')->getEncoder($usuario);
                $usuario->setSalt();
                $passwordCodificado=$encoder->encodePassword($usuario->getPassword(),$usuario->getSalt());
                $usuario->setPassword($passwordCodificado);

                //establece fechaBaja para que no pueda loguear
                $formato='Y-m-d H:i:s';
                $f1=date($formato,$usuario->getFechaAlta()->getTimestamp());
                $f2=\DateTime::createFromFormat($formato,$f1);
                $usuario->setFechaBaja($f2);

                //guarda datos en BBDD (registra la cuenta)
                $em=$this->getDoctrine()->getManager();
                $em->persist($usuario);
                $em->flush();

                //encripta datos para generar el codigo
                $datos=array($usuario->getId(),$usuario->getFechaBaja()->getTimestamp(),$usuario->getPassword());
                $datos=implode("·",$datos);  //53·1364228174·yaXjIN9tWkSD58+esrjqCEXRI+bHnkj9OZ63MHveEEmKO0jFGfOXX1Um6mo3dse/YTio2hKS4PuCXmMEKyL4sQ==
                $clave=substr($datos,-10,2);
                $codigo=$clave.$this->encriptar($datos,$clave);
                $codigo=base64_encode($codigo);
                $codigo=urlencode($codigo);   //TUWPv8TO5kUoAEICtrrfm6S0kzQBXbcMdALBRt3Nl4kwTRBgH6kijnhPVaOIifOoG06szxlW1%2FxZpToMPYOuY3H777NPMutQOz7nx6E9lZQ2%2BX0qkpaYwK3iv9xGcedOJ7witOztuCr5La5EQXtK86Pl8SYpulFQXSbVot8yOfCcbg%3D%3D
                //envia el email confirmacion con el codigo
                $message=\Swift_Message::newInstance()
                        ->setSubject('Registro cuenta en PFC')
                        ->setFrom(array($this->container->getParameter('pfc_inicio.emails.contacto') => $this->container->getParameter('pfc_inicio.proyecto')))
                        ->setTo(array($usuario->getEmail() => $usuario->getNombre()))
                        ->setBody($this->renderView('InicioBundle:Sitio:registroEmail.html.twig',array(
                            'proyecto' => $this->container->getParameter('pfc_inicio.proyecto')
                            ,'usuario' => $usuario
                            ,'codigo' => $codigo
                        )),'text/html');
                $this->get('mailer')->send($message);

                //mensaje flash
                $this->get('session')->getFlashBag()->add('info',
                        //$datos.
                        'Se le ha enviado un correo con el código de activación de cuenta.'
                        //.$codigo
                );

                return $this->redirect($this->generateUrl('usuario_registro',array('codigo' => $codigo)));
            }
        }

        return $this->render('InicioBundle:Sitio:registro.html.twig',array(
                    'formulario' => $formulario->createView()
                    ,'link' => $this->getRequest()->getBaseUrl().'/sitio/activar?'
                    ,'codigo' => $codigo
        ));
    }

    /**
     * Muestra el avatar (Gravatar) del usuario autenticado
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

    function encriptar($cadena,$clave="una clave secreta") {
        //http://copstone.com/2010/03/encriptacion-en-php/

        $cifrado=MCRYPT_RIJNDAEL_256;
        $modo=MCRYPT_MODE_ECB;

        return mcrypt_encrypt($cifrado,$clave,$cadena,$modo,mcrypt_create_iv(mcrypt_get_iv_size($cifrado,$modo),MCRYPT_RAND));
    }

    function desencriptar($cadena,$clave="una clave secreta") {

        $cifrado=MCRYPT_RIJNDAEL_256;
        $modo=MCRYPT_MODE_ECB;

        return mcrypt_decrypt($cifrado,$clave,$cadena,$modo,mcrypt_create_iv(mcrypt_get_iv_size($cifrado,$modo),MCRYPT_RAND));
    }

}