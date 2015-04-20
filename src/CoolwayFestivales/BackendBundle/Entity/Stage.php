<?php

namespace CoolwayFestivales\BackendBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * CoolwayFestivales\BackendBundle\Entity\Stage
 * @ORM\Table(name="stage")
 * @ORM\Entity(repositoryClass="CoolwayFestivales\BackendBundle\Repository\StageRepository")
 */
class Stage {

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

    /**
     * @OneToMany(targetEntity="FeastStage", mappedBy="stage", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    public $feast_stages;

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
     * @return Stage
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

    public function __toString() {
        return $this->name;
    }

    /**
     * Add feast_stages
     *
     * @param \CoolwayFestivales\BackendBundle\Entity\FeastStage $feastStages
     * @return Stage
     */
    public function addFeastStage(\CoolwayFestivales\BackendBundle\Entity\FeastStage $feastStages) {
        $this->feast_stages[] = $feastStages;

        return $this;
    }

    /**
     * Remove feast_stages
     *
     * @param \CoolwayFestivales\BackendBundle\Entity\FeastStage $feastStages
     */
    public function removeFeastStage(\CoolwayFestivales\BackendBundle\Entity\FeastStage $feastStages) {
        $this->feast_stages->removeElement($feastStages);
    }

    /**
     * Get feast_stages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFeastStages() {
        return $this->feast_stages;
    }

}
