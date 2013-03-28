<?php

namespace pfc\InicioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Viaje
 *
 * @ORM\Table(name="pfc_viaje")
 * @ORM\Entity(repositoryClass="pfc\InicioBundle\Entity\ViajeRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Viaje
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=100)
     * @Assert\NotBlank()
     */
    protected $nombre;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    protected $fecha;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="viajes")
     * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     */
    protected $usuario;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Viaje
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    
        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return Viaje
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    
        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime 
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set usuario
     *
     * @param \pfc\InicioBundle\Entity\Usuario $usuario
     * @return Viaje
     */
    public function setUsuario(\pfc\InicioBundle\Entity\Usuario $usuario=null)
    {
        $this->usuario = $usuario;
    
        return $this;
    }

    /**
     * Get usuario
     *
     * @return \pfc\InicioBundle\Entity\Usuario
     */
    public function getUsuario()
    {
        return $this->usuario;
    }
    
    public function __construct()
    {
        $this->setFecha(new \DateTime());
    }    

    public function __toString()
    {
        return $this->getNombre();
    }    
}