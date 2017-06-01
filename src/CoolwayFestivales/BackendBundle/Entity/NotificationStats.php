<?php

namespace CoolwayFestivales\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NotificationStats
 *
 * @ORM\Table(name="notification_stats")
 * @ORM\Entity
 */
class NotificationStats
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
     * @var Notification
     *
     * @ORM\ManyToOne(targetEntity="Notification")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="notification", referencedColumnName="id")
     * })
     */
    private $notification;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sent", type="datetime")
     */
    private $sent;

    /**
     * @var integer
     *
     * @ORM\Column(name="total_devices", type="integer")
     */
    private $totalDevices;


    /**
     * @var integer
     *
     * @ORM\Column(name="successful", type="integer")
     */
    private $successful;

    /**
     * @var integer
     *
     * @ORM\Column(name="failed", type="integer")
     */
    private $failed;


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
     * Set notification
     *
     * @param Notification $notification
     * @return NotificationStats
     */
    public function setNotification($notification)
    {
        $this->notification = $notification;

        return $this;
    }

    /**
     * Get notification
     *
     * @return Notification
     */
    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * Set sent
     *
     * @param \DateTime $sent
     * @return NotificationStats
     */
    public function setSent($sent)
    {
        $this->sent = $sent;

        return $this;
    }

    /**
     * Get sent
     *
     * @return \DateTime
     */
    public function getSent()
    {
        return $this->sent;
    }

    /**
     * Set totalDevices
     *
     * @param integer $totalDevices
     * @return NotificationStats
     */
    public function setTotalDevices($totalDevices)
    {
        $this->totalDevices = $totalDevices;

        return $this;
    }

    /**
     * Get totalDevices
     *
     * @return integer
     */
    public function getTotalDevices()
    {
        return $this->totalDevices;
    }


    /**
     * Set successful
     *
     * @param integer $successful
     * @return NotificationStats
     */
    public function setSuccessful($successful)
    {
        $this->successful = $successful;

        return $this;
    }

    /**
     * Get successful
     *
     * @return integer
     */
    public function getSuccessful()
    {
        return $this->successful;
    }

    /**
     * Set failed
     *
     * @param integer $failed
     * @return NotificationStats
     */
    public function setFailed($failed)
    {
        $this->failed = $failed;

        return $this;
    }

    /**
     * Get failed
     *
     * @return integer
     */
    public function getFailed()
    {
        return $this->failed;
    }
}
