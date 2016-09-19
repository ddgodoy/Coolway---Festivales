<?php

namespace CoolwayFestivales\BackendBundle\Entity;

use CoolwayFestivales\SafetyBundle\Entity\Device;
use Doctrine\ORM\Mapping as ORM;

/**
 * NotificationSchedule
 *
 * @ORM\Table(name="notification_schedule")
 * @ORM\Entity(repositoryClass="CoolwayFestivales\BackendBundle\Repository\NotificationScheduleRepository")
 */
class NotificationSchedule
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=500)
     */
    private $token;

    /**
     * @ORM\Column(name="notification_id", type="integer")
     */
    private $notificationId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="send_date", type="datetime")
     */
    private $sendDate;

    /**
     * @var status
     *
     * @ORM\Column(name="send", type="boolean")
     */
    private $status;

    /**
     * @ORM\Column(name="text", type="text", nullable=true)
     */
    private $text;




    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return NotificationSchedule
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set notificationId
     *
     * @param integer $notificationId
     * @return NotificationSchedule
     */
    public function setNotificationId($notificationId)
    {
        $this->notificationId = $notificationId;

        return $this;
    }

    /**
     * Get notificationId
     *
     * @return integer 
     */
    public function getNotificationId()
    {
        return $this->notificationId;
    }

    /**
     * Set sendDate
     *
     * @param \DateTime $sendDate
     * @return NotificationSchedule
     */
    public function setSendDate($sendDate)
    {
        $this->sendDate = $sendDate;

        return $this;
    }

    /**
     * Get sendDate
     *
     * @return \DateTime 
     */
    public function getSendDate()
    {
        return $this->sendDate;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return NotificationSchedule
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return NotificationSchedule
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }
}
