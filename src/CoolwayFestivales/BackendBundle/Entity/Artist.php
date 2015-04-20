<?php

namespace CoolwayFestivales\BackendBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * CoolwayFestivales\BackendBundle\Entity\Artist
 * @ORM\Table(name="artist")
 * @ORM\Entity(repositoryClass="CoolwayFestivales\BackendBundle\Repository\ArtistRepository")
 */
class Artist {

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
     * @OneToMany(targetEntity="FeastStageArtist", mappedBy="artist", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
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

    /**
     * Set name
     *
     * @param string $name
     * @return Artist
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
        return $this->getName();
    }

    /**
     * Add feast_stages_artist
     *
     * @param \CoolwayFestivales\BackendBundle\Entity\FeastStageArtist $feastStagesArtist
     * @return Artist
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
