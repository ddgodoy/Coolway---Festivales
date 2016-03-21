<?php

namespace CoolwayFestivales\BackendBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * CoolwayFestivales\BackendBundle\Entity\Contest
 * @ORM\Table(name="contest")
 * @ORM\Entity(repositoryClass="CoolwayFestivales\BackendBundle\Repository\ContestRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Contest {

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
     * @ORM\Column(name="name", type="string", length=250, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(name="winner", type="boolean", nullable=true)
     */
    protected $winner;

    /**
     * @var \DateTime
     * @ORM\Column(name="load_date", type="datetime",  nullable=true)
     */
    private $load_date;


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
     * @return Contest
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
     * Set name
     *
     * @param string $name
     * @return Contest
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
     * Set winner
     *
     * @param boolean $winner
     * @return Contest
     */
    public function setWinner($winner) {
        $this->winner = $winner;

        return $this;
    }

    /**
     * Get winner
     *
     * @return boolean
     */
    public function getWinner() {
        return $this->winner;
    }

    /**
     * Set load_date
     *
     * @param \DateTime $load_date
     * @return Contest
     */
    public function setLoadDate($load_date) {
        $this->load_date = $load_date;

        return $this;
    }

    /**
     * Get load_date
     *
     * @return \DateTime
     */
    public function getLoadDate() {
        return $this->load_date;
    }


} // end class