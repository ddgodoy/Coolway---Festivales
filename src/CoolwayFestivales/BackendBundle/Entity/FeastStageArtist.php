<?php

namespace CoolwayFestivales\BackendBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * CoolwayFestivales\BackendBundle\Entity\FeastStageArtist
 * @ORM\Table(name="feast_stage_artist")
 * @ORM\Entity(repositoryClass="CoolwayFestivales\BackendBundle\Repository\FeastStageArtistRepository")
 */
class FeastStageArtist {

    /**
     * @var bigint $id
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="FeastStage", cascade={"all"}, fetch="EAGER")
     */
    private $feast_stage;

    /**
     * @ManyToOne(targetEntity="Artist", cascade={"all"}, fetch="EAGER")
     */
    private $artist;

    /**
     * @var string $date
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var string $time
     * @ORM\Column(name="time", type="time")
     */
    private $time;

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
     * Set feast
     *
     * @param \CoolwayFestivales\BackendBundle\Entity\Feast $feast
     * @return FeastStage
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
     * Set stage
     *
     * @param \CoolwayFestivales\BackendBundle\Entity\Stage $stage
     * @return FeastStage
     */
    public function setStage(\CoolwayFestivales\BackendBundle\Entity\Stage $stage = null) {
        $this->stage = $stage;

        return $this;
    }

    /**
     * Get stage
     *
     * @return \CoolwayFestivales\BackendBundle\Entity\Stage
     */
    public function getStage() {
        return $this->stage;
    }


    /**
     * Set date
     *
     * @param \DateTime $date
     * @return FeastStageArtist
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     * @return FeastStageArtist
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime 
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set feast_stage
     *
     * @param \CoolwayFestivales\BackendBundle\Entity\FeastStage $feastStage
     * @return FeastStageArtist
     */
    public function setFeastStage(\CoolwayFestivales\BackendBundle\Entity\FeastStage $feastStage = null)
    {
        $this->feast_stage = $feastStage;

        return $this;
    }

    /**
     * Get feast_stage
     *
     * @return \CoolwayFestivales\BackendBundle\Entity\FeastStage 
     */
    public function getFeastStage()
    {
        return $this->feast_stage;
    }

    /**
     * Set artist
     *
     * @param \CoolwayFestivales\BackendBundle\Entity\Artist $artist
     * @return FeastStageArtist
     */
    public function setArtist(\CoolwayFestivales\BackendBundle\Entity\Artist $artist = null)
    {
        $this->artist = $artist;

        return $this;
    }

    /**
     * Get artist
     *
     * @return \CoolwayFestivales\BackendBundle\Entity\Artist 
     */
    public function getArtist()
    {
        return $this->artist;
    }
}
