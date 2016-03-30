<?php

namespace CoolwayFestivales\BackendBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * CoolwayFestivales\BackendBundle\Entity\Sandbox
 * @ORM\Table(name="sandbox")
 * @ORM\Entity(repositoryClass="CoolwayFestivales\BackendBundle\Repository\SandboxRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Sandbox
{
    /**
     * @var bigint $id
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $reference
     * @ORM\Column(name="reference", type="string", length=250, nullable=true)
     */
    private $reference;

    /**
     * @var \DateTime
     * @ORM\Column(name="created", type="datetime",  nullable=true)
     */
    private $created;

    public function __construct() {}

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set reference
     *
     * @param string $reference
     * @return Sandbox
     */
    public function setReference($reference) {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference() {
        return $this->reference;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Sandbox
     */
    public function setCreated($created) {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated() {
        return $this->created;
    }

} // end class