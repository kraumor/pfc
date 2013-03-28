<?php

namespace pfc\InicioBundle\DataFixtures\ORM;

use pfc\InicioBundle\Entity\Viaje;
use \Doctrine\Common\DataFixtures\AbstractFixture;
use \Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

//****
use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
//***
class ViajeFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;
    
    
    public function getOrder()
    {
        return 2;
    }
    
    public function load(ObjectManager $manager)
    {
        $viajes = array(
            array('nombre' => 'viatge11', 'fecha' => '2002-05-11 11:11:11', 'usuario_id' => '1' )
           ,array('nombre' => 'viatge21', 'fecha' => '2002-03-11 11:11:11', 'usuario_id' => '2' )
           ,array('nombre' => 'viatge22', 'fecha' => '2002-02-11 11:11:11', 'usuario_id' => '2' )
           ,array('nombre' => 'viatge31', 'fecha' => '2002-04-11 11:11:11', 'usuario_id' => '3' )
           ,array('nombre' => 'viatge32', 'fecha' => '2002-05-11 11:11:11', 'usuario_id' => '3' )
           ,array('nombre' => 'viatge33', 'fecha' => '2002-01-11 11:11:11', 'usuario_id' => '3' )
           ,array('nombre' => 'viatge41', 'fecha' => '2002-06-11 11:11:11', 'usuario_id' => '4' )
        );
 
        foreach ($viajes as $v) {
            
            $usuario=$manager->getRepository('InicioBundle:Usuario')->find($v['usuario_id']);
            
            $viaje = new Viaje();

            $viaje->setNombre($v['nombre'])
                  ->setFecha(new \DateTime($v['fecha']))
                  ->setUsuario($usuario)
                ;
 
            $manager->persist($viaje);
        }
 
        $manager->flush();
    }

    public function setContainer(ContainerInterface $container = null) {

        $this->container = $container;
    }
}