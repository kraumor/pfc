<?php

namespace pfc\InicioBundle\DataFixtures\ORM;

use pfc\InicioBundle\Entity\Usuario;
use \Doctrine\Common\DataFixtures\AbstractFixture;
use \Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

//****
use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
//***
class UsuarioFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;
    
    
    public function getOrder()
    {
        return 1;
    }
    
    public function load(ObjectManager $manager)
    {
        $usuarios = array(
            array('email' => 'a@a.a', 'password' => 'a', 'fechaAlta' => '2002-05-05 12:34:56', 'fechaBaja' => null, 'nombre' => 'A', 'apellidos' => 'AA AAA', 'fechaNacimiento' => '1950-01-01 12:12:12')
           ,array('email' => 'b@b.b', 'password' => 'b', 'fechaAlta' => '2002-03-05 12:34:56', 'fechaBaja' => null, 'nombre' => 'B', 'apellidos' => 'BB BBB', 'fechaNacimiento' => '1960-01-01 12:12:12')
        );
 
        foreach ($usuarios as $u) {
            
            $usuario = new Usuario();

            $encoder = $this->container->get('security.encoder_factory')->getEncoder($usuario);
            $password = $encoder->encodePassword($u['password'],$usuario->setSalt()->getSalt());
 
            $usuario->setEmail($u['email'])
                    ->setSalt()
                    ->setPassword($password)
                    ->setFechaAlta(new \DateTime($u['fechaAlta']))
                  //->setFechaBaja(new \DateTime($u['fechaBaja']))
                    ->setNombre($u['nombre'])
                    ->setApellidos($u['apellidos'])
                    ->setFechaNacimiento(new \DateTime($u['fechaNacimiento']))
//                    ->setViajes($u['v'])
                    ;
 
            $manager->persist($usuario);
        }
 
        $manager->flush();
    }

    public function setContainer(ContainerInterface $container = null) {

        $this->container = $container;
    }
}