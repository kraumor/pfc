<?php

namespace pfc\InicioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use \Symfony\Component\Security\Core\User\UserInterface;
use \Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

/**
 * Usuario
 *
 * @ORM\Table(name="pfc_usuario")
 * @ORM\Entity(repositoryClass="pfc\InicioBundle\Entity\UsuarioRepository")
 * @ORM\HasLifecycleCallbacks
 * @DoctrineAssert\UniqueEntity("email")
 */
class Usuario implements UserInterface , AdvancedUserInterface
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

//pendi:descomentar y bajar dentro de la anotacion
//     * @Assert\Email(checkMX=true)
    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    protected $email;

//pendi:descomentar y bajar dentro de la anotacion
//     * @Assert\Length(min = 6)
    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    protected $password;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=255)
     */
    protected $salt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_alta", type="datetime")
     */
    protected $fechaAlta;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_baja", type="datetime", nullable=true)
     */
    protected $fechaBaja;

    /**
     * @var integer
     *
     * @ORM\Column(name="conexiones", type="integer", nullable=true)
     */
    protected $conexiones;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ultima_conexion", type="datetime", nullable=true)
     */
    protected $ultimaConexion;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=50)
     * @Assert\NotBlank()
     */
    protected $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="apellidos", type="string", length=255, nullable=true)
     */
    protected $apellidos;

    /**
     * @var \Date
     *
     * @ORM\Column(name="fecha_nacimiento", type="date", nullable=true)
     * @Assert\Date()
     */
    protected $fechaNacimiento;
    
    /**
     * @ORM\OneToMany(targetEntity="Viaje", mappedBy="usuario")
     */
    protected $viajes;


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
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
//        $this->salt = md5(time());
//        $this->salt = 'salt';
    
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
     * Set viajes
     *
     * @param integer $viajes
     * @return Usuario
     */
    public function setViajes($viajes)
    {
        $this->viajes = $viajes;
    
        return $this;
    }

    /**
     * Get viajes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getViajes()
    {
        return $this->viajes;
    }
    
    /**
     * Add viaje
     *
     * @param \pfc\InicioBundle\Entity\Viaje $viaje
     * @return Usuario
     */
    public function addViaje(\pfc\InicioBundle\Entity\Viaje $viaje)
    {
        $this->viajes[] = $viaje;
    
        return $this;
    }

    /**
     * Remove viaje
     *
     * @param \pfc\InicioBundle\Entity\Viaje $viaje
     */
    public function removeViaje(\pfc\InicioBundle\Entity\Viaje $viaje)
    {
        $this->viajes->removeElement($viaje);
    }

    public function __construct()
    {
        $fecha=new \DateTime();
        
        $this->setSalt(md5(uniqid(null, true)));
        $this->setFechaAlta($fecha);
        $this->setConexiones(0);        
        $this->nuevaConexion($fecha);
        
        $this->viajes = new ArrayCollection();
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

    public function isAccountNonExpired() {
        return true;        
    }

    public function isAccountNonLocked() {
        return true;
    }

    public function isCredentialsNonExpired() {
        return true;
    }

    public function isEnabled() {
        return is_null($this->getFechaBaja());
    }
}
