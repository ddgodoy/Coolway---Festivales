<?php

namespace CoolwayFestivales\BackendBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * CoolwayFestivales\BackendBundle\Entity\Step
 * @ORM\Table(name="step")
 * @ORM\Entity(repositoryClass="CoolwayFestivales\BackendBundle\Repository\StepRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Step {

    /**
     * @var bigint $id
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="Feast", cascade={"all"}, fetch="EAGER")
     */
    private $feast;

    /**
     * @var string $steps
     * @ORM\Column(name="steps", type="integer")
     */
    private $steps;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $enabled;

    /**
     * @var text
     *
     * @ORM\Column(name="text", type="text")
     */
    private $text;

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
     * @return Step
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
     * @return Step
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
     * @param \CoolwayFestivales\BackendBundle\Entity\StepArtist $feastStagesArtist
     * @return Step
     */
    public function addStepsArtist(\CoolwayFestivales\BackendBundle\Entity\StepArtist $feastStagesArtist) {
        $this->feast_stages_artist[] = $feastStagesArtist;

        return $this;
    }

    /**
     * Remove feast_stages_artist
     *
     * @param \CoolwayFestivales\BackendBundle\Entity\StepArtist $feastStagesArtist
     */
    public function removeStepsArtist(\CoolwayFestivales\BackendBundle\Entity\StepArtist $feastStagesArtist) {
        $this->feast_stages_artist->removeElement($feastStagesArtist);
    }

    /**
     * Get feast_stages_artist
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStepsArtist() {
        return $this->feast_stages_artist;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Step
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
     * Set enabled
     *
     * @param boolean $enabled
     * @return Step
     */
    public function setEnabled($enabled) {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled() {
        return $this->enabled;
    }

    /**
     * Set terms_conditions
     *
     * @param string $termsConditions
     * @return Step
     */
    public function setTermsConditions($termsConditions) {
        $this->terms_conditions = $termsConditions;

        return $this;
    }

    /**
     * Get terms_conditions
     *
     * @return string
     */
    public function getTermsConditions() {
        return $this->terms_conditions;
    }

    /**
     * Set steps
     *
     * @param integer $steps
     * @return Step
     */
    public function setSteps($steps) {
        $this->steps = $steps;

        return $this;
    }

    /**
     * Get steps
     *
     * @return integer
     */
    public function getSteps() {
        return $this->steps;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Step
     */
    public function setText($text) {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText() {
        return $this->text;
    }

}
