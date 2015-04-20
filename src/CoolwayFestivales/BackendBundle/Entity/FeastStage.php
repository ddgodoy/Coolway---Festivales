<?php

namespace CoolwayFestivales\BackendBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * CoolwayFestivales\BackendBundle\Entity\FeastStage
 * @ORM\Table(name="feast_stage")
 * @ORM\Entity(repositoryClass="CoolwayFestivales\BackendBundle\Repository\FeastStageRepository")
 */
class FeastStage {

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
     * @ManyToOne(targetEntity="Stage",fetch="EAGER")
     */
    private $stage;

    /**
     * @OneToMany(targetEntity="FeastStageArtist", mappedBy="feast_stage",  orphanRemoval=true)
     */
    public $feast_stages_artist;

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

    public function __toString() {
        return $this->feast . "-" . $this->stage;
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
     * Add feast_stages_artist
     *
     * @param \CoolwayFestivales\BackendBundle\Entity\FeastStageArtist $feastStagesArtist
     * @return FeastStage
     */
    public function addFeastStagesArtist(\CoolwayFestivales\BackendBundle\Entity\FeastStageArtist $feastStagesArtist) {
        $this->feast_stages_artist[] = $feastStagesArtist;

        return $this;
    }

    /**
     * Remove feast_stages_artist
     *
     * @param \CoolwayFestivales\BackendBundle\Entity\FeastStageArtist $feastStagesArtist
     */
    public function removeFeastStagesArtist(\CoolwayFestivales\BackendBundle\Entity\FeastStageArtist $feastStagesArtist) {
        $this->feast_stages_artist->removeElement($feastStagesArtist);
    }

    /**
     * Get feast_stages_artist
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFeastStagesArtist() {
        return $this->feast_stages_artist;
    }

}
