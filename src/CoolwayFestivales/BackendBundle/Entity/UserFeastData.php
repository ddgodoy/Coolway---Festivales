<?php

namespace CoolwayFestivales\BackendBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * CoolwayFestivales\BackendBundle\Entity\UserFeastData
 * @ORM\Table(name="user_feast_data")
 * @ORM\Entity(repositoryClass="CoolwayFestivales\BackendBundle\Repository\UserFeastDataRepository")
 */
class UserFeastData {

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
     * @ManyToOne(targetEntity="\CoolwayFestivales\SafetyBundle\Entity\User",  fetch="EAGER")
     */
    private $user;

    /**
     * @var string $total
     * @ORM\Column(name="total", type="float")
     */
    private $total;

    /**
     * @var string $dance
     * @ORM\Column(name="dance", type="float")
     */
    private $dance;

    /**
     * @var string $music
     * @ORM\Column(name="music", type="float")
     */
    private $music;

    /**
     * @var string $total_share
     * @ORM\Column(name="total_share", type="integer")
     */
    private $total_share;

    /**
     * @var string $latitude
     * @ORM\Column(name="latitude", type="string", length=50, nullable=false)
     */
    private $latitude;

    /**
     * @var string $longitude
     * @ORM\Column(name="longitude", type="string", length=50, nullable=false)
     */
    private $longitude;

    /**
     * @var \DateTime
     * @ORM\Column(name="date_from", type="datetime",  nullable=true)
     */
    private $date;

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
     * Set total
     *
     * @param float $total
     * @return UserFeastData
     */
    public function setTotal($total) {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return float
     */
    public function getTotal() {
        return $this->total;
    }

    /**
     * Set dance
     *
     * @param float $dance
     * @return UserFeastData
     */
    public function setDance($dance) {
        $this->dance = $dance;

        return $this;
    }

    /**
     * Get dance
     *
     * @return float
     */
    public function getDance() {
        return $this->dance;
    }

    /**
     * Set music
     *
     * @param float $music
     * @return UserFeastData
     */
    public function setMusic($music) {
        $this->music = $music;

        return $this;
    }

    /**
     * Get music
     *
     * @return float
     */
    public function getMusic() {
        return $this->music;
    }

    /**
     * Set total_share
     *
     * @param integer $totalShare
     * @return UserFeastData
     */
    public function setTotalShare($totalShare) {
        $this->total_share = $totalShare;

        return $this;
    }

    /**
     * Get total_share
     *
     * @return integer
     */
    public function getTotalShare() {
        return $this->total_share;
    }

    /**
     * Set latitude
     *
     * @param string $latitude
     * @return UserFeastData
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
     * @return UserFeastData
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

    /**
     * Set user
     *
     * @param \CoolwayFestivales\SafetyBundle\Entity\User $user
     * @return UserFeastData
     */
    public function setUser(\CoolwayFestivales\SafetyBundle\Entity\User $user = null) {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \CoolwayFestivales\SafetyBundle\Entity\User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return FeastStageArtist
     */
    public function setDate($date) {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate() {
        return $this->date;
    }

}
