<?php

namespace pfc\InicioBundle\DataFixtures\ORM;

use pfc\InicioBundle\Entity\Usuario;
use \Doctrine\Common\DataFixtures\AbstractFixture;
use \Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class UsuarioFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function getOrder()
    {
        return 1;
    }
    
    public function load(ObjectManager $manager)
    {
        $usuarios = array(
            array('email' => 'a@a.a', 'password' => 'a', 'salt' => 'a', 'fechaAlta' => '2002-05-05 12:34:56', 'fechaBaja' => null, 'nombre' => 'A', 'apellidos' => 'AA AAA', 'fechaNacimiento' => '1900-01-01 12:12:12', 'viajeId' => '1')
           ,array('email' => 'b@b.b', 'password' => 'b', 'salt' => 'a', 'fechaAlta' => '2002-03-05 12:34:56', 'fechaBaja' => null, 'nombre' => 'B', 'apellidos' => 'BB BBB', 'fechaNacimiento' => '1960-01-01 12:12:12', 'viajeId' => '2')
           ,array('email' => 'c@c.c', 'password' => 'c', 'salt' => 'a', 'fechaAlta' => '2002-02-05 12:34:56', 'fechaBaja' => null, 'nombre' => 'C', 'apellidos' => 'CC CCC', 'fechaNacimiento' => '1970-01-01 12:12:12', 'viajeId' => '3')
           ,array('email' => 'd@d.d', 'password' => 'd', 'salt' => 'a', 'fechaAlta' => '2002-04-05 12:34:56', 'fechaBaja' => null, 'nombre' => 'D', 'apellidos' => 'DD DDD', 'fechaNacimiento' => '1980-01-01 12:12:12', 'viajeId' => '4')
           ,array('email' => 'e@e.e', 'password' => 'e', 'salt' => 'a', 'fechaAlta' => '2002-05-05 12:34:56', 'fechaBaja' => null, 'nombre' => 'E', 'apellidos' => 'EE EEE', 'fechaNacimiento' => '1990-01-01 12:12:12', 'viajeId' => '5')
           ,array('email' => 'f@f.f', 'password' => 'f', 'salt' => 'a', 'fechaAlta' => '2002-01-05 12:34:56', 'fechaBaja' => null, 'nombre' => 'F', 'apellidos' => 'FF FFF', 'fechaNacimiento' => '1950-01-01 12:12:12', 'viajeId' => '6')
           ,array('email' => 'g@g.g', 'password' => 'g', 'salt' => 'a', 'fechaAlta' => '2002-06-05 12:34:56', 'fechaBaja' => null, 'nombre' => 'G', 'apellidos' => 'GG GGG', 'fechaNacimiento' => '1940-01-01 12:12:12', 'viajeId' => '7')
        );
 
        foreach ($usuarios as $u) {
            $usuario = new Usuario();
 
            $usuario->setEmail($u['email'])
                    ->setPassword($u['password'])
                    ->setSalt($u['salt'])
                    ->setFechaAlta(new \DateTime($u['fechaAlta']))
                  //->setFechaBaja(new \DateTime($u['fechaBaja']))
                    ->setNombre($u['nombre'])
                    ->setApellidos($u['apellidos'])
                    ->setFechaNacimiento(new \DateTime($u['fechaNacimiento']))
                    ->setViajeId($u['viajeId']);
 
            $manager->persist($usuario);
        }
 
        $manager->flush();
    }
}