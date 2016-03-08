<?php

namespace CoolwayFestivales\BackendBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * CoolwayFestivales\BackendBundle\Entity\Googlemap
 * @ORM\Table(name="googlemap")
 * @ORM\Entity(repositoryClass="CoolwayFestivales\BackendBundle\Repository\GooglemapRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Googlemap {

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
     * @var string $name
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
     * @var string $detail
     * @ORM\Column(name="detail", type="string", length=255)
     */
    private $detail;

    /**
     * @var string $latitude
     * @ORM\Column(name="latitude", type="string", length=50)
     */
    private $latitude;

    /**
     * @var string $longitude
     * @ORM\Column(name="longitude", type="string", length=50)
     */
    private $longitude;

    /**
     * @var string $path
     * @ORM\Column(name="path", type="string", nullable=true)
     */
    private $path;

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
     * @return Googlemap
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
     * Set path
     *
     * @param string $path
     * @return Googlemap
     */
    public function setPath($path) {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Googlemap
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
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
     * Set detail
     *
     * @param string $detail
     * @return Googlemap
     */
    public function setDetail($detail) {
        $this->detail = $detail;

        return $this;
    }

    /**
     * Get detail
     *
     * @return string
     */
    public function getDetail() {
        return $this->detail;
    }

    /**
     * Set latitude
     *
     * @param string $latitude
     * @return Googlemap
     */
    public function setLatitude($latitude) {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return string
     */
    public function getLatitude() {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param string $longitude
     * @return Googlemap
     */
    public function setLongitude($longitude) {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return string
     */
    public function getLongitude() {
        return $this->longitude;
    }

} //end class