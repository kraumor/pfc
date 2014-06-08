<?php

namespace pfc\InicioBundle\DataFixtures\ORM;

use pfc\InicioBundle\Entity\Viaje;
use \Doctrine\Common\DataFixtures\AbstractFixture;
use \Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

class ViajeFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface {

    private $container;

    public function setContainer(ContainerInterface $container=null) {

        $this->container=$container;
    }

    public function getOrder() {
        return 2;
    }

    public function load(ObjectManager $manager) {

        $viajes=array(
             array('nombre' => 'Lleida'             ,'fecha' => '2014-05-01 00:00:00','usuario_id' => '1','opciones'=>'a:10:{s:2:"s0";a:3:{s:6:"ciudad";s:6:"Lleida";s:3:"lat";s:5:"41.62";s:3:"lon";s:4:"0.63";}s:2:"s1";N;s:2:"s2";N;s:2:"s3";N;s:2:"s4";N;s:2:"s5";N;s:2:"s6";N;s:2:"s7";N;s:2:"s8";N;s:2:"s9";N;}')
            ,array('nombre' => 'Andorra la vella'   ,'fecha' => '2014-05-01 00:00:01','usuario_id' => '1','opciones'=>'a:10:{s:2:"s0";a:3:{s:6:"ciudad";s:7:"andorra";s:3:"lat";s:5:"42.51";s:3:"lon";s:4:"1.52";}s:2:"s1";N;s:2:"s2";N;s:2:"s3";N;s:2:"s4";N;s:2:"s5";N;s:2:"s6";N;s:2:"s7";N;s:2:"s8";N;s:2:"s9";N;}')
            ,array('nombre' => 'Sant Cugat'         ,'fecha' => '2014-05-01 00:00:02','usuario_id' => '1','opciones'=>'a:10:{s:2:"s0";a:3:{s:6:"ciudad";s:10:"Sant Cugat";s:3:"lat";s:5:"41.47";s:3:"lon";s:4:"2.09";}s:2:"s1";N;s:2:"s2";N;s:2:"s3";N;s:2:"s4";N;s:2:"s5";N;s:2:"s6";N;s:2:"s7";N;s:2:"s8";N;s:2:"s9";N;}')
            ,array('nombre' => 'Frankfurt am Main'  ,'fecha' => '2014-05-01 00:00:03','usuario_id' => '1','opciones'=>'a:10:{s:2:"s0";a:3:{s:6:"ciudad";s:17:"Frankfurt am Main";s:3:"lat";s:5:"50.12";s:3:"lon";s:4:"8.64";}s:2:"s1";N;s:2:"s2";N;s:2:"s3";N;s:2:"s4";N;s:2:"s5";N;s:2:"s6";N;s:2:"s7";N;s:2:"s8";N;s:2:"s9";N;}')
            ,array('nombre' => 'xxx'                ,'fecha' => '2014-05-01 00:00:04','usuario_id' => '1','opciones'=>null)
        );

        foreach($viajes as $v){

            $usuario=$manager->getRepository('InicioBundle:Usuario')->find($v['usuario_id']);

            $viaje=new Viaje();

            $viaje->setNombre($v['nombre'])
                    ->setFecha(new \DateTime($v['fecha']))
                    ->setUsuario($usuario)
                    ->setOpciones($v['opciones'])
            ;

            $manager->persist($viaje);
            //flush ahora, lo necesita el acl
            $manager->flush();

            //asignar ACL
            $proveedor=$this->container->get('security.acl.provider');

            $idObjeto=ObjectIdentity::fromDomainObject($viaje);
            $idUsuario=UserSecurityIdentity::fromAccount($usuario);

            //busca ACL del par (viaje usuario) y si no tiene, lo crea
            try{
                $acl=$proveedor->findAcl($idObjeto,array($idUsuario));
            } catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e){
                $acl=$proveedor->createAcl($idObjeto);
            }
            //borrar ACES previos
            $aces=$acl->getObjectAces();
            foreach($aces as $index => $ace){
                $acl->deleteObjectAce($index);
            }
            //insertar ACE
            $acl->insertObjectAce($idUsuario,MaskBuilder::MASK_OPERATOR);
            $proveedor->updateAcl($acl);
        }
    }
}