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
     * @ORM\Column(name="total_android", type="integer")
     */
    private $totalAndroid;

    /**
     * @var integer
     *
     * @ORM\Column(name="successful_android", type="integer")
     */
    private $successfulAndroid;

    /**
     * @var integer
     *
     * @ORM\Column(name="failed_android", type="integer")
     */
    private $failedAndroid;

    /**
     * @var integer
     *
     * @ORM\Column(name="total_ios", type="integer")
     */
    private $totalIOS;

    /**
     * @var integer
     *
     * @ORM\Column(name="successful_ios", type="integer")
     */
    private $successfulIOS;

    /**
     * @var integer
     *
     * @ORM\Column(name="failed_ios", type="integer")
     */
    private $failedIOS;

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
     * Set totalAndroid
     *
     * @param integer $totalAndroid
     * @return NotificationStats
     */
    public function setTotalAndroid($totalAndroid)
    {
        $this->totalAndroid = $totalAndroid;

        return $this;
    }

    /**
     * Get totalAndroid
     *
     * @return integer
     */
    public function getTotalAndroid()
    {
        return $this->totalAndroid;
    }

    /**
     * Set successfulAndroid
     *
     * @param integer $successfulAndroid
     * @return NotificationStats
     */
    public function setSuccessfulAndroid($successfulAndroid)
    {
        $this->successfulAndroid = $successfulAndroid;

        return $this;
    }

    /**
     * Get successfulAndroid
     *
     * @return integer
     */
    public function getSuccessfulAndroid()
    {
        return $this->successfulAndroid;
    }

    /**
     * Set failedAndroid
     *
     * @param integer $failedAndroid
     * @return NotificationStats
     */
    public function setFailedAndroid($failedAndroid)
    {
        $this->failedAndroid = $failedAndroid;

        return $this;
    }

    /**
     * Get failedAndroid
     *
     * @return integer
     */
    public function getFailedAndroid()
    {
        return $this->failedAndroid;
    }

    /**
     * Set totalIOS
     *
     * @param integer $totalIOS
     * @return NotificationStats
     */
    public function setTotalIOS($totalIOS)
    {
        $this->totalIOS = $totalIOS;

        return $this;
    }

    /**
     * Get totalIOS
     *
     * @return integer
     */
    public function getTotalIOS()
    {
        return $this->totalIOS;
    }

    /**
     * Set successfulIOS
     *
     * @param integer $successfulIOS
     * @return NotificationStats
     */
    public function setSuccessfulIOS($successfulIOS)
    {
        $this->successfulIOS = $successfulIOS;

        return $this;
    }

    /**
     * Get successfulIOS
     *
     * @return integer
     */
    public function getSuccessfulIOS()
    {
        return $this->successfulIOS;
    }

    /**
     * Set failedIOS
     *
     * @param integer $failedIOS
     * @return NotificationStats
     */
    public function setFailedIOS($failedIOS)
    {
        $this->failedIOS = $failedIOS;

        return $this;
    }

    /**
     * Get failedIOS
     *
     * @return integer
     */
    public function getFailedIOS()
    {
        return $this->failedIOS;
    }
}
