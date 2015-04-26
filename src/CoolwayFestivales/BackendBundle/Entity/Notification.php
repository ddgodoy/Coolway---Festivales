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

    /**
     * Set send
     *
     * @param boolean $send
     * @return Award
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

}
