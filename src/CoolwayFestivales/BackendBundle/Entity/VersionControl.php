<?php

namespace CoolwayFestivales\BackendBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * CoolwayFestivales\BackendBundle\Entity\VersionControl
 * @ORM\Table(name="version_control")
 * @ORM\Entity(repositoryClass="CoolwayFestivales\BackendBundle\Repository\VersionControlRepository")
 * @ORM\HasLifecycleCallbacks
 */
class VersionControl
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
     * @ManyToOne(targetEntity="Feast", fetch="EAGER")
     */
    private $feast;

    /**
     * @var string $version
     * @ORM\Column(name="version", type="string", length=50, nullable=true)
     */
    private $version;

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
     * Set feast
     *
     * @param \CoolwayFestivales\BackendBundle\Entity\Feast $feast
     * @return VersionControl
     */
    public function setFeast(\CoolwayFestivales\BackendBundle\Entity\Feast $feast = null) {
        $this->feast = $feast;

        return $this;
    }

    /**
     * Get feast
     *
     * @return \CoolwayFestivales\BackendBundle\Entity\Feast
     */
    public function getFeast() {
        return $this->feast;
    }

    /**
     * Set version
     *
     * @param string $version
     * @return VersionControl
     */
    public function setVersion($version) {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return string
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return VersionControl
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