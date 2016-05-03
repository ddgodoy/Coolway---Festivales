<?php

namespace CoolwayFestivales\BackendBundle\Repository;

use CoolwayFestivales\BackendBundle\Entity\NotificationStats;
use Doctrine\ORM\EntityRepository;

/**
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NotificationRepository extends EntityRepository
{
    public function findInFestival($feast_id)
    {
        $q = $this->getEntityManager()->createQuery
        (
            "SELECT n FROM BackendBundle:Notification n
			WHERE n.feast = $feast_id
			ORDER BY n.id ASC"
        );
        return $q->getResult();
    }
    //
    public function findForBatch()
    {
        $dNow = date('Y-m-d H:i:00');

        $q = $this->getEntityManager()->createQuery
        (
            "SELECT n FROM BackendBundle:Notification n
			WHERE n.send = 0 AND n.date IS NOT NULL AND n.date = '$dNow'
			ORDER BY n.id ASC"
        );
        return $q->getResult();
    }
    //
    public function sendToMobile($entity)
    {
        $em  = $this->getEntityManager();
        $notification = $em->getRepository('BackendBundle:Notification')->findOneBy(array('id' => $entity->getId()));
        $devices = $em->getRepository('SafetyBundle:Device')->findBy(array('feast_id' => $entity->getFeast()->getId()));
        $androidTokens = array();
        $iosTokens = array();

        foreach($devices as $device) {
            if($device->getOs() == 1)
                $iosTokens[] = $device->getToken();
            else
                $androidTokens[] = $device->getToken();
        }
        if ($notification)
        {
            $gcmStats = array();
            $apnStats = array();

            if (count($androidTokens) > 0) {
                $gcm = $this->get('coolway_app.gcm');
                $gcmStats = $gcm->sendNotification($androidTokens,
                    $notification->getName(),
                    $notification->getText(),
                    'admin-notification',
                    'com.gravedad.lesarts',
                    false,
                    600,
                    false);
            }

            if (count($iosTokens) > 0) {
                $apn = $this->get('coolway_app.apn');
                $apnStats = $apn->sendNotification($iosTokens,
                    $notification->getText(),
                    5,
                    'com.gravedad.lesarts',
                    'bingbong.aiff');
            }

            if(count($apnStats) > 0 || count($gcmStats) > 0 )
            {
                $stats = new NotificationStats();
                $stats->setNotification($notification);
                $stats->setTotalDevices($gcmStats["total"] + $apnStats["total"]);
                $stats->setTotalAndroid($gcmStats["total"]);
                $stats->setSuccessfulAndroid($gcmStats["successful"]);
                $stats->setFailedAndroid($gcmStats["failed"]);
                $stats->setTotalIOS($apnStats["total"]);
                $stats->setSuccessfulIOS($apnStats["successful"]);
                $stats->setFailedIOS($apnStats["failed"]);
                $stats->setSent(new \DateTime("now"));
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($stats);
                $notification->setDelivery(true);
            }else
                $notification->setDelivery(false);


            $notification->setSend(true);
            $em->persist($notification);
            $em->flush();
        }
    }
}