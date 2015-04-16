<?php

namespace CoolwayFestivales\SafetyBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\Version;
use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * @ORM\Entity(repositoryClass="CoolwayFestivales\SafetyBundle\Repository\RoleRepository")
 * @ORM\Table(name="role")
 */
class Role implements RoleInterface,  \Serializable{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", unique=true, length=100, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = "2",
     *      max = "50",
     *      minMessage = "Your first name must be at least {{ limit }} characters length",
     *      maxMessage = "Your first name cannot be longer than than {{ limit }} characters length"
     * )
     */
    protected $name;

    /**
     * @ORM\Column(type="text")
     */
    protected $description;

    public function __construct() {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param text $description
     */
    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return text 
     */
    public function getDescription() {
        return $this->description;
    }

    public function getRole() {
        return $this->getName();
    }
    
    public function __toString() {
        return $this->name;
    }
    
    public function getClass(){
        return "SafetyBundle:Role";
    }

  public function serialize()
  {
    /*
     * ! Don't serialize $users field !
     */
    return \serialize(array(
        $this->id,
        $this->name
      ));
  }

  /**
   * @see \Serializable::unserialize()
   */
  public function unserialize($serialized)
  {
    list(
      $this->id,
      $this->name
      ) = \unserialize($serialized);
  }

}