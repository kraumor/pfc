<?php

namespace pfc\InicioBundle\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\MinLength;
use Symfony\Component\Validator\Constraints\MaxLength;

class Contacto
{
    protected $nombre;

    protected $email;

    protected $asunto;

    protected $mensaje;

    public function getNombre()
    {
        return $this->nombre;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getAsunto()
    {
        return $this->asunto;
    }

    public function setAsunto($asunto)
    {
        $this->asunto = $asunto;
    }

    public function getMensaje()
    {
        return $this->mensaje;
    }

    public function setMensaje($mensaje)
    {
        $this->mensaje = $mensaje;
    }
    
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('nombre', new NotBlank(array('message' => 'no dejar en blanco')));

        //$metadata->addPropertyConstraint('email', new Email()));
        $metadata->addPropertyConstraint('email', new Email(array('message' => 'formato no válido')));

        $metadata->addPropertyConstraint('asunto', new NotBlank(array('message' => 'no dejar en blanco')));
        $metadata->addPropertyConstraint('asunto', new MaxLength(50));

        $metadata->addPropertyConstraint('mensaje', new MinLength(10));
    }    
}