<?php

namespace CoolwayFestivales\BackendBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * CoolwayFestivales\BackendBundle\Entity\Weather
 * @ORM\Table(name="weather")
 * @ORM\Entity(repositoryClass="CoolwayFestivales\BackendBundle\Repository\WeatherRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Weather
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
     * @ManyToOne(targetEntity="Feast", fetch="EAGER")
     */
    private $feast;

    /**
     * @var \Date
     * @ORM\Column(name="forecast_date", type="date",  nullable=true)
     */
    private $forecast_date;

    /**
     * @var string $weekday
     * @ORM\Column(name="weekday", type="string", length=50, nullable=true)
     */
    private $weekday;

    /**
     * @var integer $min_temp
     * @ORM\Column(name="min_temp", type="integer", nullable=true)
     */
    private $min_temp;

    /**
     * @var integer $max_temp
     * @ORM\Column(name="max_temp", type="integer", nullable=true)
     */
    private $max_temp;

    /**
     * @var string $condition_day
     * @ORM\Column(name="condition_day", type="string", length=250, nullable=true)
     */
    private $condition_day;

    /**
     * @var string $condition_icon
     * @ORM\Column(name="condition_icon", type="string", length=250, nullable=true)
     */
    private $condition_icon;

    /**
     * @var integer $humidity
     * @ORM\Column(name="humidity", type="integer", nullable=true)
     */
    private $humidity;

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
     * @return Weather
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
     * Set forecast_date
     *
     * @param \Date $forecast_date
     * @return Weather
     */
    public function setForecastDate($forecast_date) {
        $this->forecast_date = $forecast_date;

        return $this;
    }

    /**
     * Get forecast_date
     *
     * @return \Date
     */
    public function getForecastDate() {
        return $this->forecast_date;
    }

    /**
     * Set weekday
     *
     * @param string $weekday
     * @return Weather
     */
    public function setWeekday($weekday) {
        $this->weekday = $weekday;

        return $this;
    }

    /**
     * Get weekday
     *
     * @return string
     */
    public function getWeekday() {
        return $this->weekday;
    }

    /**
     * Set min_temp
     *
     * @param integer $min_temp
     * @return Weather
     */
    public function setMinTemp($min_temp) {
        $this->min_temp = $min_temp;

        return $this;
    }

    /**
     * Get min_temp
     *
     * @return integer
     */
    public function getMinTemp() {
        return $this->min_temp;
    }

    /**
     * Set max_temp
     *
     * @param integer $max_temp
     * @return Weather
     */
    public function setMaxTemp($max_temp) {
        $this->max_temp = $max_temp;

        return $this;
    }

    /**
     * Get max_temp
     *
     * @return integer
     */
    public function getMaxTemp() {
        return $this->max_temp;
    }

    /**
     * Set condition_day
     *
     * @param string $condition_day
     * @return Weather
     */
    public function setConditionDay($condition_day) {
        $this->condition_day = $condition_day;

        return $this;
    }

    /**
     * Get condition_day
     *
     * @return string
     */
    public function getConditionDay() {
        return $this->condition_day;
    }

    /**
     * Set condition_icon
     *
     * @param string $condition_icon
     * @return Weather
     */
    public function setConditionIcon($condition_icon) {
        $this->condition_icon = $condition_icon;

        return $this;
    }

    /**
     * Get condition_icon
     *
     * @return string
     */
    public function getConditionIcon() {
        return $this->condition_icon;
    }

    /**
     * Set humidity
     *
     * @param integer $humidity
     * @return Weather
     */
    public function setHumidity($humidity) {
        $this->humidity = $humidity;

        return $this;
    }

    /**
     * Get humidity
     *
     * @return integer
     */
    public function getHumidity() {
        return $this->humidity;
    }

    /**
     * Set load_date
     *
     * @param \DateTime $load_date
     * @return Weather
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