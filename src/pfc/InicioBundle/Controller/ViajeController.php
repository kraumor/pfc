<?php

namespace pfc\InicioBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use pfc\InicioBundle\Entity\Viaje;
use pfc\InicioBundle\Form\ViajeType;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity,
    Symfony\Component\Security\Acl\Domain\UserSecurityIdentity,
    Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Viaje controller.
 *
 */
class ViajeController extends Controller {

    /**
     * Lists all Viaje entities.
     *
     */
    public function listarAction() {
        $em=$this->getDoctrine()->getManager();
        $usuario=$this->get('security.context')->getToken()->getUser();

//        $entities = $em->getRepository('InicioBundle:Viaje')->findAll();
        $entities=$em->getRepository('InicioBundle:Usuario')->findViajesRecientes($usuario->getId());

        return $this->render('InicioBundle:Viaje:listar.html.twig',array(
                    'entities' => $entities,
        ));
    }

    /**
     * Creates a new Viaje entity.
     *
     */
    public function crearAction(Request $request) {
        
        $entity=new Viaje();
        $form=$this->createForm(new ViajeType(),$entity);
        $usuario=$this->get('security.context')->getToken()->getUser();

        $form->bind($request);

        if($form->isValid()){

            $entity->setUsuario($usuario)
                    ->setFecha(new \DateTime());
            $em=$this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            //ACL
            $idObjeto=ObjectIdentity::fromDomainObject($entity);
            $idUsuario=UserSecurityIdentity::fromAccount($usuario);
            $acl=$this->get('security.acl.provider')->createAcl($idObjeto);
            $acl->insertObjectAce($idUsuario,MaskBuilder::MASK_OPERATOR);
            $this->get('security.acl.provider')->updateAcl($acl);

            return $this->redirect($this->generateUrl('viaje_mostrar',array('id' => $entity->getId())));
        }

        return $this->render('InicioBundle:Viaje:crear.html.twig',array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a new Viaje entity.
     *
     */
    public function nuevoAction() {
        
        $entity=new Viaje();
        $form=$this->createForm(new ViajeType(),$entity);

        return $this->render('InicioBundle:Viaje:crear.html.twig',array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Opciones
     *
     */
    public function opcionesAction($id) {
        
        $em=$this->getDoctrine()->getManager();

        $entity=$em->getRepository('InicioBundle:Viaje')->find($id);

        //comprueba permiso ACL
        if(false===$this->get('security.context')->isGranted('EDIT',$entity)){
            throw new AccessDeniedException();
        }

        if(!$entity){
            throw $this->createNotFoundException('Viaje no encontrado.');
        }          
        
        $opciones=$entity->getOpciones();
        $opciones=unserialize($opciones);
        
        $request=$this->getRequest();      

        if($request->getMethod()== 'POST' ){
   
            $ciudad=$request->get('ciudad');
            $accion=$request->get('acc');
            switch($accion){
                case 'b':
                    $places=$this->getPlaces($ciudad);
                    $servicios=$this->getServicios();

                    $respuesta=$this->render('InicioBundle:Viaje:opciones.html.twig',array(
                         'entity'       => $entity
                        ,'acc'          => $accion
                        ,'ciudad'       => $ciudad
                        ,'places'       => $places
                        ,'servicios'    => $servicios
                        ,'opciones'     => $opciones
                    ));                          
                    return $respuesta;
                    break;
                case 'g':
                    $aux0=null;
                    foreach($request->request->all() as $k => $v){
                        $aux0[$k]=$v;
                    }

                    $aux=null;
                    for($i=0;$i<=9;$i++){
                        $par='s'.$i;
                        if(isset($aux0[$par])){
                            $v=$aux0[$par];
                            if($i==0){
                                $klat=$par.'-'.$v.'_lat';
                                $klon=$par.'-'.$v.'_lon';
                                $vlat=$aux0[$klat];
                                $vlon=$aux0[$klon];
                                if(is_numeric($vlat) && is_numeric($vlon)
                                   && ($vlat > -90  && $vlat < 90 )
                                   && ($vlon > -180 && $vlat < 180)       ){
                                    $aux[$par]=array(
                                                'ciudad'=>$ciudad
                                                ,'lat'  =>$aux0[$klat]
                                                ,'lon'  =>$aux0[$klon]
                                                );   
                                }                                             
                            }
                            else{
                                $aux[$par]=null;     
                            }
                        }
                    }
                    if( !isset($aux['s0']) ){
                        $respuesta=$this->render('InicioBundle:Viaje:opciones.html.twig',array(
                                            'entity'    => $entity
                                           ,'opciones'  => $opciones
                                           ,'ciudad'    => $ciudad
                        ));
                        return $respuesta;
                    }
                    else{
                        //guardar  opciones ($aux) en BBDD
                        $opciones=serialize($aux);
                        $entity->setOpciones($opciones);
                        $em=$this->getDoctrine()->getManager();
                        $em->persist($entity);
                        $em->flush();

                        //recuperar     
                        $entity=$em->getRepository('InicioBundle:Viaje')->find($id);
                        $opciones=null;
                        $opciones=$entity->getOpciones();
                        $opciones=unserialize($opciones);    
                        $places=$this->getPlaces($ciudad);
                        $servicios=$this->getServicios();
                    }
            
                    $respuesta=$this->render('InicioBundle:Viaje:opciones.html.twig',array(
                         'entity'       => $entity
                        ,'acc'          => 'b'
                        ,'ciudad'       => $ciudad
                        ,'places'       => $places
                        ,'servicios'    => $servicios
                        ,'opciones'     => $opciones
                    ));                          
                    return $respuesta;
                        
                    break;
            }
            $respuesta=$this->render('InicioBundle:Viaje:opciones.html.twig',array(
                               'entity'    => $entity
                              ,'opciones'  => $opciones
                              ,'ciudad'    => $opciones['s0']['ciudad']
                              ,'acc'       => 'b'
                              ,'servicios' => $this->getServicios()
            ));            
        }
        else{
            $respuesta=$this->render('InicioBundle:Viaje:opciones.html.twig',array(
                                'entity'    => $entity
                               ,'opciones'  => $opciones
            )); 
        }
        return $respuesta;
    }

    /**
     * Lista servicios disponibles
     *
     */
    public function getServicios() {
        
        $ser=null;
        
        $s=null;
        $s[]=array('dis'=> 1 ,'id'=> 1  ,'nom'=> 'Zona horaria' );
        $s[]=array('dis'=> 1 ,'id'=> 2  ,'nom'=> 'Mapa' );
        $s[]=array('dis'=> 1 ,'id'=> 3  ,'nom'=> 'Jerarquía administrativa' );
        $s[]=array('dis'=> 1 ,'id'=> 4  ,'nom'=> 'Tiempo' );
        $s[]=array('dis'=> 1 ,'id'=> 5  ,'nom'=> 'Lugares' );
        $s[]=array('dis'=> 1 ,'id'=> 6  ,'nom'=> 'Divisas' );
        $s[]=array('dis'=> 1 ,'id'=> 7  ,'nom'=> 'Wikipedia (articulos cercanos)' );
        $s[]=array('dis'=> 1 ,'id'=> 8  ,'nom'=> 'Wikipedia (articulos relacionados)' );
        $s[]=array('dis'=> 1 ,'id'=> 9  ,'nom'=> 'Wikitravel' );
        
        foreach($s as $ss){
            if($ss['dis']){$ser[$ss['id']]=$ss;}
            
        }
        if(isset($ser)){ksort($ser);}

        return $ser;
    }
    
    /**
     * Busca el text en diferentes servicios web y devuelve los resultados
     * encontrados con sus coordenadas.
     *
     */
    public function getPlaces($text) {
        
        $places=null;
        
        $p1=$this->getPlacesOSM('osm',$text);
        $p2=$this->getPlacesWWO('wwo',$text);
        $p3=$this->getPlacesGEO('geo',$text);
        
        for($i=1;$i<=3;$i++){
            if(!is_null(${'p'.$i})){$places[]=${'p'.$i};}
        }

        return $places;
    }
    
    /**
     * Devuelve array de resultados
     *
     */
    public function arrayColumnas1($c1,$c2,$c3) {
        
        return array(
                      'lat'         =>$c1
                     ,'lon'         =>$c2
                     ,'name'        =>$c3                     
                     );
    }    
    
    /**
     * Busca el text en el servicio web y devuelve los resultados
     * encontrados con sus coordenadas.
     * Servicio web: http://wiki.openstreetmap.org/wiki/Nominatim
     */
    public function getPlacesOSM($t,$text) {
        
        $res[$t]['txt']='OpenStreetMap';
        $res[$t]['res']=null;
        
        $p0='city='.$text;
        $p1='limit=10';
        $p2='accept-language=es,en,ca';
      //$p2=null;
        $p3='format=xml';
        $params=implode('&', array($p0,$p1,$p2,$p3));
        $url="http://nominatim.openstreetmap.org/search?".$params;

        $ch=curl_init();  //ch:curl_handle
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,2);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $xml_response = curl_exec($ch);
        curl_close($ch);   
        
        if ($xml_response){
            $xml = new \SimpleXMLElement($xml_response);
            foreach($xml->place as $v){
                $res[$t]['res'][]=$this->arrayColumnas1(
                         (float)    $v['lat']
                        ,(float)    $v['lon']
                        ,(string)   $v['display_name']
                        );
                        // 'place_id'    =>(int)$v['place_id']
            }
        }
        return !is_null($res[$t]['res']) ? $res : null;
    }

    /**
     * Busca el text en el servicio web y devuelve los resultados
     * encontrados con sus coordenadas.
     * Servicio web: http://api.worldweatheronline.com
     */
    public function getPlacesWWO($t,$text) {
        
        $res[$t]['txt']='World Weather Online';
        $res[$t]['res']=null;
        
        $p0='q='.$text;   //Pass US Zipcode, UK Postcode, Canada Postalcode, IP address, Latitude/Longitude (decimal degree) or city name
        $p1='popular=no';    //Search only for major cities. E.g:- popular=yes
        $p2='format=xml';    //Output format as JSON, XML, CSV or TAB
        $p3='key=ambbk6wet7ntteuufxx7nka9';
      //$p4='num_of_results=2'; //Number of results to get back. Max value is 200. E.g:- num_of_results=3
        $params=implode('&', array($p0,$p1,$p2,$p3));
        $url="http://api.worldweatheronline.com/free/v1/search.ashx?".$params;

        $ch=curl_init();  //ch:curl_handle
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,2);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $xml_response = curl_exec($ch);
        curl_close($ch); 
        
        if ($xml_response){
            $xml = new \SimpleXMLElement($xml_response);
            foreach($xml->result as $v){
                $res[$t]['res'][]=$this->arrayColumnas1(
                         (float)   $v->latitude
                        ,(float)   $v->longitude
                        ,(string)  $v->areaName.', '.(string)$v->region.', '.(string)$v->country
                        );
                        //'areaName'        =>(string)  $v->areaName
                        //,'country'        =>(string)  $v->country
                        //,'region'         =>(string)  $v->region
                        //,'latitude'       =>(float)   $v->latitude
                        //,'longitude'      =>(float)   $v->longitude
                        //,'population'     =>(double)  $v->population
                        //,'weatherUrl'     =>(string)  $v->weatherUrl                
            }
        }
        return !is_null($res[$t]['res']) ? $res : null;
    }
    /**
     * Busca el text en el servicio web y devuelve los resultados
     * encontrados con sus coordenadas.
     * Servicio web: http://www.geonames.org/export/wikipedia-webservice.html
     */
    public function getPlacesGEO($t,$text) {
        
        $res[$t]['txt']='GeoNames';
        $res[$t]['res']=null;
        
        $p0='username=pfc185';
        $p1='maxRows=10';   //Number of results to get back. Max value is 200. E.g:- num_of_results=3
        $p2='q='.$text;  //q : place name (urlencoded utf8)
        $p3='lang=es';
        $params=implode('&', array($p0,$p1,$p2,$p3));
        $url="http://api.geonames.org/wikipediaSearch?".$params;

        $ch=curl_init();  //ch:curl_handle
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,2);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $xml_response = curl_exec($ch);
        curl_close($ch);
        
        if ($xml_response){
            $xml = new \SimpleXMLElement($xml_response);
            foreach($xml->entry as $v){
                $res[$t]['res'][]=$this->arrayColumnas1(
                         (float)  $v->lat
                        ,(float)  $v->lng
                        ,(string) $v->title
                        );
                        //'title'          =>(string)  $v->title
                        //,'lat'            =>(float)  $v->lat
                        //,'lng'            =>(float)  $v->lng
                        //,'wikipediaUrl'   =>(string)   $v->wikipediaUrl            
            }
        }
        return !is_null($res[$t]['res']) ? $res : null;
    }
    /**
     * GetDatos
     *
     */
    public function getDatos($opciones) {
        
        $d=$opciones;
        $aux=null;
        
        if(!is_null($d) && count($d)>1 ){
            
            $ciudad =$d['s0']['ciudad'];
            $lat    =$d['s0']['lat'];
            $lon    =$d['s0']['lon'];

            foreach($d as $k => $v){
                if(strcmp($k,'s0')==0){
                    $aux[$k]['res']=array(
                                         'ciudad'    => $ciudad
                                        ,'lat'       => $lat
                                        ,'lon'       => $lon
                                        );                   
                }
                else{
                    switch ($k) {
                        case 's1':$aux1=$this->get01ZonaHoraria($lat,$lon);       break;
                        case 's2':$aux1=$this->get02Mapa($lat,$lon);              break;
                        case 's3':$aux1=$this->get03Jerarquia($lat,$lon);         break;
                        case 's4':$aux1=$this->get04Tiempo($lat,$lon);            break;
                        case 's5':$aux1=$this->get05Lugares($lat,$lon);           break;
                        case 's6':$aux1=$this->get06Divisas();                    break;
                        case 's7':$aux1=$this->get07Wikipedia1($lat,$lon,$ciudad);break;
                        case 's8':$aux1=$this->get08Wikipedia2($ciudad);          break;
                        case 's9':$aux1=$this->get09Wikitravel($ciudad);          break;
                    }
                    $aux[$k]=$aux1;
                }
            }    
        
        }        
        return $aux;
    }

    /**
     * Devuelve zona horaria
     */
    public function get01ZonaHoraria($lat,$lon) {
        
        $res['txt']='Zona Horaria';
        $res['res']=null;
        
      //$p0='q='.$ciudad;
        $p0='q='.$lat.','.$lon;
        $p1='format=xml';
        $p2='key=ambbk6wet7ntteuufxx7nka9';
        $params=implode('&', array($p0,$p1,$p2));
        $url="http://api.worldweatheronline.com/free/v1/tz.ashx?".$params;        

        $ch=curl_init();  //ch:curl_handle
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_HEADER,0);        
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,2);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $xml_response = curl_exec($ch);
        curl_close($ch);
        
        if ($xml_response){
            $xml = new \SimpleXMLElement($xml_response);
            foreach($xml->time_zone as $v){
                $res['res']=array(
                         'datetime' =>(string) $v->localtime
                        ,'offset'   =>(float)  $v->utcOffset
                        );          
            }
        }
        
        return !is_null($res['res']) ? $res : null;
    }
    
    /**
     * Devuelve imagen
     */
    public function get02Mapa($lat,$lon) {
        
        $res['txt']='Mapa';
        
        $p0='mlat='.$lat;
        $p1='mlon='.$lon;
        $p2='z=7';
        $p3='w=600';
        $p4='h=400';
        $p5='mode=Export';
        $p6='show=1';
        $params=implode('&', array($p0,$p1,$p2,$p3,$p4,$p5,$p6));
        $url="http://ojw.dev.openstreetmap.org/StaticMap/?".$params;
        //<img src="http://ojw.dev.openstreetmap.org/StaticMap/?mlat={{lat}}&mlon={{lon}}&z=0&show_icon_list=11&w=300&h=200&mode=Export&show=1" width="480" height="300" alt="OpenStreetMap" />
        $res['res']=$url;
        
        return !is_null($res['res']) ? $res : null;
    }
    /**
     * Devuelve jerarquia administrativa
     */
    public function get03Jerarquia($lat,$lon) {
        
        $res['txt']='Jerarquía Administrativa';
        $res['res']=null;
        
        $p0='username=pfc185';
        $p1='lat='.$lat;
        $p2='lng='.$lon;
        $p3='lang=es';
        $params=implode('&', array($p0,$p1,$p2,$p3));
        $url="http://api.geonames.org/extendedFindNearby?".$params;
        //$url="http://api.geonames.org/findNearby?".$params;

        $ch=curl_init();  //ch:curl_handle
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_HEADER,0);        
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,2);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $xml_response = curl_exec($ch);
        curl_close($ch);
        
        if ($xml_response){
            $xml = new \SimpleXMLElement($xml_response);
            foreach($xml->xpath('//name') as $v){
                $res['res'][]=(
                         (string) $v
                        );          
            }
        }
        
        return !is_null($res['res']) ? $res : null;
    }
    
    /**
     * Devuelve previsión meteorológica
     */
    public function get04Tiempo($lat,$lon) {
        
        $res['txt']='Meteorología';
        $res['res']=null;
        
      //$p0='q='.$ciudad;
        $p0='q='.$lat.','.$lon;
        $p1='format=xml';
        $p2='num_of_days=4';    // Changes the number of day forecast you need.
        $p3='key=ambbk6wet7ntteuufxx7nka9';
        $params=implode('&', array($p0,$p1,$p2,$p3));
        $url="http://api.worldweatheronline.com/free/v1/weather.ashx?".$params;      

        $ch=curl_init();  //ch:curl_handle
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_HEADER,0);        
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,2);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $xml_response = curl_exec($ch);
        curl_close($ch);
        
        if ($xml_response){
            $xml = new \SimpleXMLElement($xml_response);
            foreach($xml->current_condition as $v){
                $res['res']['current']=array(
                      'observation_time'  =>  (string) $v->observation_time
                     ,'temp_C'            =>  (int)    $v->temp_C
                     ,'weatherIconUrl'    =>  (string) $v->weatherIconUrl
                     ,'weatherDesc'       =>  (string) $v->weatherDesc
                     ,'windspeedKmph'     =>  (int)    $v->windspeedKmph
                     ,'winddirDegree'     =>  (int)    $v->winddirDegree
                     ,'winddir16Point'    =>  (string) $v->winddir16Point
                     ,'precipMM'          =>  (float)  $v->precipMM
                     ,'humidity'          =>  (int)    $v->humidity
                     ,'visibility'        =>  (int)    $v->visibility
                     ,'pressure'          =>  (int)    $v->pressure
                     ,'cloudcover'        =>  (int)    $v->cloudcover
                    );          
            }
            foreach($xml->weather as $v){
                $res['res']['forecast'][]=array(
                      'date'              => (string) $v->date
                     ,'tempMinC'          => (int)    $v->tempMinC
                     ,'tempMaxC'          => (int)    $v->tempMaxC
                     ,'windspeedKmph'     => (int)    $v->windspeedKmph
                     ,'winddirection'     => (string) $v->winddirection
                     ,'winddir16Point'    => (string) $v->winddir16Point
                     ,'winddirDegree'     => (int)    $v->winddirDegree
                     ,'precipMM'          => (float)  $v->precipMM
                     ,'weatherIconUrl'    => (string) $v->weatherIconUrl
                     ,'weatherDesc'       => (string) $v->weatherDesc
                    );          
            }
        }
        
        return !is_null($res['res']) ? $res : null;
    }
    
    /**
     * Devuelve lugares cercanos
     */
    public function get05Lugares($lat,$lon) {
        
        $res['txt']='Lugares cercanos';
        $res['res']=null;
        
        for($i=500;$i<=15000;$i=$i+14500){
       //$p0='q='.$ciudad;
        $p0='username=pfc185';
        $p1='lat='.$lat;
        $p2='lng='.$lon;
        $p3='maxRows=20';
        $p4='radius=100';  //km
        $p5='lang=es';
        $p6='cities=cities'.$i;     //100,1000,5000,15000
        $params=implode('&', array($p0,$p1,$p2,$p3,$p4,$p5,$p6));
        $url="http://api.geonames.org/findNearbyPlaceName?".$params;  

        $ch=curl_init();  //ch:curl_handle
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_HEADER,0);        
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,2);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $xml_response = curl_exec($ch);
        curl_close($ch);
        
        if ($xml_response){
            $xml = new \SimpleXMLElement($xml_response);
            foreach($xml->xpath('//geoname') as $v){
                $res['res'][$i][]=array(
                      'lug'     =>(string) $v->name
                     ,'dis'     =>(float)  $v->distance                        
                   //,'lat'     =>(float)  $v->lat
                   //,'lon'     =>(float)  $v->lng
                    );          
            }
        }           
        }

        return !is_null($res['res']) ? $res : null;
    }
    
    /**
     * Devuelve divisas
     */
    public function get06Divisas() {
        
        $res['txt']='Cambio de divisas';
        $res['res']=null;
        
        $url="http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml";
        
        $xml_response=simplexml_load_file($url);
        
        if ($xml_response){

            foreach($xml_response->Cube->Cube as $v){
                $res['res']['date']=(string) $v['time'];        
            }
            foreach($xml_response->Cube->Cube->Cube as $v){
                $res['res']['rates'][(string) $v['currency']]=(string) $v['rate'];              
            }   
            if(isset($res['res']['rates'])){asort($res['res']['rates']);}            
        }           
        
        return !is_null($res['res']) ? $res : null;
    }

    /**
     * Devuelve resultados wikipedia 1
     */
    public function get07Wikipedia1($lat,$lon,$ciudad) {
        
        $res['txt']='Wikipedia (articulos cercanos)';
        $res['res']=null;
        
      //$p0='q='.$ciudad;
        $p0='username=pfc185';
        $p1='lat='.$lat;
        $p2='lng='.$lon;
        $p3='maxRows=10';
        $p4='radius=10';  //km
        $p5='lang=es';
        $params=implode('&', array($p0,$p1,$p2,$p3,$p4,$p5));
        $url="http://api.geonames.org/findNearbyWikipedia?".$params;       

        $ch=curl_init();  //ch:curl_handle
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_HEADER,0);        
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,2);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $xml_response = curl_exec($ch);
        curl_close($ch);
        
        if ($xml_response){
            $xml = new \SimpleXMLElement($xml_response);
            foreach($xml->xpath('//entry') as $v){
                $res['res'][]=array(
                         'title'              =>  (string) $v->title
                        ,'wikipediaUrl'       =>  (string) $v->wikipediaUrl
                        ,'thumbnailImg'       =>  (string) $v->thumbnailImg                        
                        ,'summary'            =>  (string) $v->summary  
                      //,'leve'               =>  levenshtein($ciudad,(string) $v->title)                        
                        );          
            }
        }
        
        return !is_null($res['res']) ? $res : null;
    }

    /**
     * Devuelve resultados wikipedia 2
     */
    public function get08Wikipedia2($ciudad) {
        
        $res['txt']='Wikipedia (articulos relacionados)';
        $res['res']=null;
        
        $p0='action=query';
        $p1='generator=search';
        $p2='format=xml';
        $p3='srlimit=20';
        $p4='prop=info';
        $p5='inprop=url';
        $p6='gsrsearch='.urlencode($ciudad);
        $params=implode('&', array($p0,$p1,$p2,$p3,$p4,$p5,$p6));
        $url="http://es.wikipedia.org/w/api.php?".$params;     

        $ch=curl_init();  //ch:curl_handle
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_HEADER,0);        
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,2);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $xml_response = curl_exec($ch);
        curl_close($ch); 
        
        if ($xml_response){
            $xml = new \SimpleXMLElement($xml_response);
            foreach($xml->xpath('//page') as $v){
                $leve=levenshtein($ciudad,(string) $v['title']);
                $res['res'][$leve]=array(
                         'title'             =>  (string) $v['title']
                        ,'fullurl'           =>  (string) $v['fullurl']
                        ,'pageid'            =>  (string) $v['pageid']        
                      //,'leve'               =>  levenshtein($ciudad,(string) $v['title'])                        
                        );          
            }
            if(isset($res['res'])){ksort($res['res']);}            
        }
        
        return !is_null($res['res']) ? $res : null;
    }

    /**
     * Devuelve resultados wikitravel
     */
    public function get09Wikitravel($ciudad) {
        
        $res['txt']='Wikitravel';
        $res['res']=null;
        
        $p0='action=query';
        $p1='generator=search';
        $p2='format=xml';
        $p3='srlimit=20';
        $p4='prop=info';
        $p5='inprop=url';
        $p6='gsrsearch='.urlencode($ciudad);
        $params=implode('&', array($p0,$p1,$p2,$p3,$p4,$p5,$p6));
        $url="http://wikitravel.org/wiki/es/api.php?".$params;    

        $ch=curl_init();  //ch:curl_handle
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_HEADER,0);        
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,2);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $xml_response = curl_exec($ch);
        curl_close($ch); 
        
        if ($xml_response){
            $xml = new \SimpleXMLElement($xml_response);
            foreach($xml->xpath('//page') as $v){
                $leve=levenshtein($ciudad,(string) $v['title']);
                $res['res'][$leve]=array(
                         'title'             =>  (string) $v['title']
                        ,'fullurl'           =>  (string) $v['fullurl']
                        ,'pageid'            =>  (string) $v['pageid']        
                      //,'leve'               =>  levenshtein($ciudad,(string) $v['title'])                        
                        );          
            }
            if(isset($res['res'])){ksort($res['res']);}
        }
        
        return !is_null($res['res']) ? $res : null;
    }
    
    /**
     * Finds and displays a Viaje entity.
     *
     */
    public function mostrarAction($id) {
        
        $em=$this->getDoctrine()->getManager();

        $entity=$em->getRepository('InicioBundle:Viaje')->find($id);

        //comprueba permiso ACL
        if(false===$this->get('security.context')->isGranted('VIEW',$entity)){
            throw new AccessDeniedException();
        }

        if(!$entity){
            throw $this->createNotFoundException('Viaje no encontrado.');
        }

        $deleteForm=$this->createDeleteForm($id);

        $opciones=$entity->getOpciones();
        $opciones=unserialize($opciones);

        $data=$this->getDatos($opciones);
             
        if($data){
            $respuesta=$this->render('InicioBundle:Viaje:mostrar.html.twig',array(
                         'entity'       => $entity
                        ,'delete_form'  => $deleteForm->createView()
                        ,'data'         => $data
                    ));
        }
        else{
            $respuesta=$this->forward('InicioBundle:Viaje:opciones',array('id' => $id));
        }
        
        return $respuesta;
    }

    /**
     * Displays a form to edit an existing Viaje entity.
     *
     */
    public function editarAction($id) {
        
        $em=$this->getDoctrine()->getManager();

        $entity=$em->getRepository('InicioBundle:Viaje')->find($id);

        if(false===$this->get('security.context')->isGranted('EDIT',$entity)){
            throw new AccessDeniedException();
        }
        
        if(!$entity){
            throw $this->createNotFoundException('Viaje no encontrado.');
        }

        $editForm=$this->createForm(new ViajeType(),$entity);
        $deleteForm=$this->createDeleteForm($id);

        return $this->render('InicioBundle:Viaje:editar.html.twig',array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Viaje entity.
     *
     */
    public function actualizarAction(Request $request,$id) {
        
        $em=$this->getDoctrine()->getManager();

        $entity=$em->getRepository('InicioBundle:Viaje')->find($id);

        if(false===$this->get('security.context')->isGranted('EDIT',$entity)){
            throw new AccessDeniedException();
        }
        
        if(!$entity){
            throw $this->createNotFoundException('Viaje no encontrado.');
        }

        $deleteForm=$this->createDeleteForm($id);
        $editForm=$this->createForm(new ViajeType(),$entity);
        $editForm->bind($request);

        if($editForm->isValid()){
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('viaje_editar',array('id' => $id)));
        }

        return $this->render('InicioBundle:Viaje:editar.html.twig',array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Viaje entity.
     *
     */
    public function borrarAction(Request $request,$id) {
        
        $form=$this->createDeleteForm($id);
        $form->bind($request);
        
        if($form->isValid()){
            $em=$this->getDoctrine()->getManager();
            $entity=$em->getRepository('InicioBundle:Viaje')->find($id);

            if(false===$this->get('security.context')->isGranted('DELETE',$entity)){
                throw new AccessDeniedException();
            }      
            
            if(!$entity){
                throw $this->createNotFoundException('Viaje no encontrado.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('viaje'));
    }

    /**
     * Creates a form to delete a Viaje entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id','hidden')
                        ->getForm()
        ;
    }

}
