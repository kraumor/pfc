<?php

namespace pfc\InicioBundle\Entity;

use \Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
//use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Usuario
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="pfc\InicioBundle\Entity\UsuarioRepository")
 * @ORM\HasLifecycleCallbacks
 * @DoctrineAssert\UniqueEntity("email")
 */
class Usuario implements UserInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

//pendi:descomentar y bajar dentro de la anotacion
//     * @Assert\Email(checkMX=true)
    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

//pendi:descomentar y bajar dentro de la anotacion
//     * @Assert\Length(min = 6)
    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=255)
     */
    private $salt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_alta", type="datetime")
     */
    private $fechaAlta;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_baja", type="datetime", nullable=true)
     */
    private $fechaBaja;

    /**
     * @var integer
     *
     * @ORM\Column(name="conexiones", type="integer", nullable=true)
     */
    private $conexiones;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ultima_conexion", type="datetime", nullable=true)
     */
    private $ultimaConexion;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=100)
     * @Assert\NotBlank()
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="apellidos", type="string", length=255, nullable=true)
     */
    private $apellidos;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_nacimiento", type="datetime", nullable=true)
     * @Assert\Date()
     */
    private $fechaNacimiento;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="viaje_id", type="integer", nullable=true)
     */
    private $viajeId;


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
     * Set email
     *
     * @param string $email
     * @return Usuario
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Usuario
     */
    public function setPassword($password)
    {
        $this->password = $password;
    
        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set salt
     *
     * @return Usuario
     */
    public function setSalt()
    {
//pendi: descomentar para generar salt aleatorio        
//        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
//        $this->salt = md5(time());
        $this->salt = 'salt';
    
        return $this;
    }

    /**
     * Get salt
     *
     * @return string 
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set fechaAlta
     *
     * @param \DateTime $fechaAlta
     * @return Usuario
     */
    public function setFechaAlta($fechaAlta)
    {
        $this->fechaAlta = $fechaAlta;
    
        return $this;
    }

    /**
     * Get fechaAlta
     *
     * @return \DateTime 
     */
    public function getFechaAlta()
    {
        return $this->fechaAlta;
    }

    /**
     * Set fechaBaja
     *
     * @param \DateTime $fechaBaja
     * @return Usuario
     */
    public function setFechaBaja($fechaBaja)
    {
        $this->fechaBaja = $fechaBaja;
    
        return $this;
    }

    /**
     * Get fechaBaja
     *
     * @return \DateTime 
     */
    public function getFechaBaja()
    {
        return $this->fechaBaja;
    }

    /**
     * Set conexiones
     *
     * @param integer $conexiones
     * @return Usuario
     */
    public function setConexiones($conexiones)
    {
        $this->conexiones = $conexiones;
    
        return $this;
    }

    /**
     * Get conexiones
     *
     * @return integer
     */
    public function getConexiones()
    {
        return $this->conexiones;
    }
    
    public function addConexiones($num=1)
    {
        $this->conexiones += $num;
    
        return $this;
    }
    
    /**
     * Set ultimaConexion
     *
     * @param \DateTime $ultimaConexion
     * @return Usuario
     */
    public function setUltimaConexion($ultimaConexion)
    {
        $this->ultimaConexion = $ultimaConexion;
    
        return $this;
    }

    /**
     * Get ultimaConexion
     *
     * @return \DateTime 
     */
    public function getUltimaConexion()
    {
        return $this->ultimaConexion;
    }
    
    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Usuario
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
     * Set apellidos
     *
     * @param string $apellidos
     * @return Usuario
     */
    public function setApellidos($apellidos)
    {
        $this->apellidos = $apellidos;
    
        return $this;
    }

    /**
     * Get apellidos
     *
     * @return string 
     */
    public function getApellidos()
    {
        return $this->apellidos;
    }

    /**
     * Set fechaNacimiento
     *
     * @param \DateTime $fechaNacimiento
     * @return Usuario
     */
    public function setFechaNacimiento($fechaNacimiento)
    {
        $this->fechaNacimiento = $fechaNacimiento;
    
        return $this;
    }

    /**
     * Get fechaNacimiento
     *
     * @return \DateTime 
     */
    public function getFechaNacimiento()
    {
        return $this->fechaNacimiento;
    }

    /**
     * Set viajeId
     *
     * @param integer $viajeId
     * @return Usuario
     */
    public function setViajeId($viajeId)
    {
        $this->viajeId = $viajeId;
    
        return $this;
    }

    /**
     * Get viajeId
     *
     * @return integer 
     */
    public function getViajeId()
    {
        return $this->viajeId;
    }
    
    public function __construct()
    {
        $fecha=new \DateTime();
        
        $this->setSalt(md5(uniqid(null, true)));
        $this->setFechaAlta($fecha);
        $this->setConexiones(0);        
        $this->nuevaConexion($fecha);
        
        //$this->viajeId = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getNombre();
    }

    public function eraseCredentials() {
        
    }
    
    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array($this->id, ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list ($this->id, ) = unserialize($serialized);
    }    

    public function getRoles() {
        return array('ROLE_USUARIO');
    }

    public function getUsername() {
        return $this->getEmail();
    }
               
    public function nuevaConexion($fecha)
    {
        $this->addConexiones();
        $this->setUltimaConexion($fecha);
    
        return $this;
    }
}
