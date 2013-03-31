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
            array('nombre' => 'viatge11','fecha' => '2002-05-11 11:11:11','usuario_id' => '1')
            ,array('nombre' => 'viatge21','fecha' => '2002-03-11 11:11:11','usuario_id' => '2')
            ,array('nombre' => 'viatge22','fecha' => '2002-02-11 11:11:11','usuario_id' => '2')
            ,array('nombre' => 'viatge31','fecha' => '2002-04-11 11:11:11','usuario_id' => '3')
            ,array('nombre' => 'viatge32','fecha' => '2002-05-11 11:11:11','usuario_id' => '3')
            ,array('nombre' => 'viatge33','fecha' => '2002-01-11 11:11:11','usuario_id' => '3')
            ,array('nombre' => 'viatge41','fecha' => '2002-06-11 11:11:11','usuario_id' => '4')
        );

        foreach($viajes as $v){

            $usuario=$manager->getRepository('InicioBundle:Usuario')->find($v['usuario_id']);

            $viaje=new Viaje();

            $viaje->setNombre($v['nombre'])
                    ->setFecha(new \DateTime($v['fecha']))
                    ->setUsuario($usuario)
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