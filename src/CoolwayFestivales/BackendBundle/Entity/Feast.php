<?php

namespace CoolwayFestivales\BackendBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\HttpFoundation\Image\UploadedImage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * CoolwayFestivales\BackendBundle\Entity\Feast
 * @ORM\Table(name="feast")
 * @ORM\Entity(repositoryClass="CoolwayFestivales\BackendBundle\Repository\FeastRepository")
 */
class Feast
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
     * @var string $name
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    private $name;

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
     * @var \Date
     * @ORM\Column(name="date_from", type="date",  nullable=false)
     */
    private $date_from;

    /**
     * @var \Date
     * @ORM\Column(name="date_to", type="date",  nullable=true)
     */
    private $date_to;
	
    /**
     * @var string $path
     * @ORM\Column(name="path", type="string", nullable=true)
     */
    private $path;

    /**
     * @var string $image
     * @ORM\Column(name="image", type="string", length=250, nullable=true)
     */
    private $image;

    /**
     * @OneToMany(targetEntity="FeastStage", mappedBy="feast", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    public $feast_stages;

    /**
     * @OneToMany(targetEntity="UserFeastData", mappedBy="feast", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    public $feast_userdfeastdata;


    /**
     * @ORM\Column(name="schedule_active", type="boolean", nullable=true)
     */
    protected $schedule_active;

    /**
     * @ORM\Column(name="gcm_token",type="string", length=255, nullable=true)
     */
    private $gcm_token;

    /**
     * @ORM\Column(name="apn_app_id",type="string", length=255, nullable=true)
     */
    private $apn_app_id;

    /**
     * @ORM\Column(name="apn_pem_file",type="string", length=255, nullable=true)
     */
    private $apn_pem_file;

    /**
     * @ORM\Column(name="apn_sandbox",type="boolean", nullable=true)
     */
    private $apn_sandbox;

    /**
     * @ORM\Column(name="apn_pass_phrase",type="string", length=255, nullable=true)
     */
    private $apn_pass_phrase;

    /**
     * @ORM\Column(name="gcm_app_id",type="string", length=255, nullable=true)
     */
    private $gcm_app_id;

    public function __construct() {}

    public function __toString() {
        return $this->name;
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
     * @return Feast
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

    function getLatitude() {
        return $this->latitude;
    }

    function setLatitude($latitude) {
        $this->latitude = $latitude;
    }

    function getLongitude() {
        return $this->longitude;
    }

    function setLongitude($longitude) {
        $this->longitude = $longitude;
    }

    /**
     * Set date_from
     *
     * @param \Date $date_from
     * @return Feast
     */
    public function setDateFrom($date_from) {
        $this->date_from = $date_from;

        return $this;
    }

    /**
     * Get date_from
     *
     * @return \Date
     */
    public function getDateFrom() {
        return $this->date_from;
    }

    /**
     * Set date_to
     *
     * @param \Date $date_to
     * @return Feast
     */
    public function setDateTo($date_to) {
        $this->date_to = $date_to;

        return $this;
    }

    /**
     * Get date_to
     *
     * @return \Date
     */
    public function getDateTo() {
        return $this->date_to;
    }

    /**
     * Set image
     *
     * @param string $image
     * @return Feast
     */
    public function setImage($image) {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage() {
        return $this->image;
    }

    /**
     * Add feast_stages
     *
     * @param \CoolwayFestivales\BackendBundle\Entity\FeastStage $feastStages
     * @return Feast
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

    /**
     * Add feast_userdfeastdata
     *
     * @param \CoolwayFestivales\BackendBundle\Entity\UserFeastData $feastUserdfeastdata
     * @return Feast
     */
    public function addFeastUserdfeastdatum(\CoolwayFestivales\BackendBundle\Entity\UserFeastData $feastUserdfeastdata) {
        $this->feast_userdfeastdata[] = $feastUserdfeastdata;

        return $this;
    }

    /**
     * Remove feast_userdfeastdata
     *
     * @param \CoolwayFestivales\BackendBundle\Entity\UserFeastData $feastUserdfeastdata
     */
    public function removeFeastUserdfeastdatum(\CoolwayFestivales\BackendBundle\Entity\UserFeastData $feastUserdfeastdata) {
        $this->feast_userdfeastdata->removeElement($feastUserdfeastdata);
    }

    /**
     * Get feast_userdfeastdata
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFeastUserdfeastdata() {
        return $this->feast_userdfeastdata;
    }
	
    /**
     * Set path
     *
     * @param string $path
     * @return User
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


    public function getScheduleActive(){
        return $this->schedule_active;
    }


    public function setScheduleActive($schedule_active){
        $this->schedule_active = $schedule_active;
    }

    /**
     * Set gcm_token
     *
     * @param string $gcmToken
     * @return Feast
     */
    public function setGcmToken($gcmToken)
    {
        $this->gcm_token = $gcmToken;

        return $this;
    }

    /**
     * Get gcm_token
     *
     * @return string 
     */
    public function getGcmToken()
    {
        return $this->gcm_token;
    }

    /**
     * Set apn_app_id
     *
     * @param string $apnAppId
     * @return Feast
     */
    public function setApnAppId($apnAppId)
    {
        $this->apn_app_id = $apnAppId;

        return $this;
    }

    /**
     * Get apn_app_id
     *
     * @return string 
     */
    public function getApnAppId()
    {
        return $this->apn_app_id;
    }

    /**
     * Set apn_sandbox
     *
     * @param boolean $apnSandbox
     * @return Feast
     */
    public function setApnSandbox($apnSandbox)
    {
        $this->apn_sandbox = $apnSandbox;

        return $this;
    }

    /**
     * Get apn_sandbox
     *
     * @return boolean 
     */
    public function getApnSandbox()
    {
        return $this->apn_sandbox;
    }

    /**
     * Set apn_pass_phrase
     *
     * @param string $apnPassPhrase
     * @return Feast
     */
    public function setApnPassPhrase($apnPassPhrase)
    {
        $this->apn_pass_phrase = $apnPassPhrase;

        return $this;
    }

    /**
     * Get apn_pass_phrase
     *
     * @return string 
     */
    public function getApnPassPhrase()
    {
        return $this->apn_pass_phrase;
    }



    /**
     * Set apn_pem_file
     *
     * @param string $apnPemFile
     * @return Feast
     */
    public function setApnPemFile($apnPemFile)
    {
        $this->apn_pem_file = $apnPemFile;

        return $this;
    }

    /**
     * Get apn_pem_file
     *
     * @return string 
     */
    public function getApnPemFile()
    {
        return $this->apn_pem_file;
    }

    /**
     * Set gcm_app_id
     *
     * @param string $gcmAppId
     * @return Feast
     */
    public function setGcmAppId($gcmAppId)
    {
        $this->gcm_app_id = $gcmAppId;

        return $this;
    }

    /**
     * Get gcm_app_id
     *
     * @return string 
     */
    public function getGcmAppId()
    {
        return $this->gcm_app_id;
    }
}
