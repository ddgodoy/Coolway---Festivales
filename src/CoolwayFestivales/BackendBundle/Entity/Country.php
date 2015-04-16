<?php

namespace CoolwayFestivales\BackendBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * CoolwayFestivales\BackendBundle\Entity\Country
 * @ORM\Table(name="country")
 * @ORM\Entity(repositoryClass="CoolwayFestivales\BackendBundle\Repository\CountryRepository")
 */
class Country {

    /**
     * @var bigint $id
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $name
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    private $name;

    public function __construct() {

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
     * @return Country
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

}
