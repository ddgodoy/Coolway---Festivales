<?php

namespace CoolwayFestivales\BackendBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * CoolwayFestivales\BackendBundle\Entity\Notification
 * @ORM\Table(name="notification")
 * @ORM\Entity(repositoryClass="CoolwayFestivales\BackendBundle\Repository\NotificationRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Notification {

    /**
     * @var bigint $id
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var name
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @ManyToOne(targetEntity="Feast", fetch="EAGER")
     */
    private $feast;

    /**
     * @var text
     *
     * @ORM\Column(name="text", type="text")
     */
    private $text;

    /**
     * @var send
     *
     * @ORM\Column(name="send", type="boolean")
     */
    private $send;

    /**
     * @var send
     *
     * @ORM\Column(name="delivery", type="boolean")
     */
    private $delivery;

    /**
     * @var \DateTime
     * @ORM\Column(name="date_cron", type="datetime",  nullable=true)
     */
    private $date;

    public function __construct() {
        $this->send = 0;
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
     * @return Notification
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
     * Set date
     *
     * @param \DateTime $date
     * @return Notification
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

    /**
     * Set feast
     *
     * @param \CoolwayFestivales\BackendBundle\Entity\Feast $feast
     * @return Notification
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
     * Set text
     *
     * @param string $text
     * @return Notification
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

    /**
     * Set send
     *
     * @param boolean $send
     * @return Notification
     */
    public function setSend($send) {
        $this->send = $send;

        return $this;
    }

    /**
     * Get send
     *
     * @return boolean
     */
    public function getSend() {
        return $this->send;
    }


    /**
     * Set delivery
     *
     * @param boolean $delivery
     * @return Notification
     */
    public function setDelivery($delivery) {
        $this->delivery = $delivery;

        return $this;
    }

    /**
     * Get delivery
     *
     * @return boolean
     */
    public function getDelivery() {
        return $this->delivery;
    }

} // end class