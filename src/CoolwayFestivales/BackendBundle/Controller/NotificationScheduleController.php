<?php

namespace CoolwayFestivales\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\HttpFoundation\Response;
use CoolwayFestivales\BackendBundle\Entity\NotificationStats;
use CoolwayFestivales\BackendBundle\Entity\Feast;
use CoolwayFestivales\BackendBundle\Repository\FeastRepository;

/**
 * Notification Schedule controller.
 *
 * @Route("/admin/notification-schedule")
 */
class NotificationScheduleController extends Controller
{
    /**
     * @Route("/send/scheduled", name="notification_send_scheduled")
     * @Method("GET")
     */
    public function notificationSendScheduled()
    {
        return $this->taskSendScheduled();
    }

    /**
     * @Route("/send/scheduled2", name="notification_send_scheduled2")
     * @Method("GET")
     */
    public function notificationSendScheduled2()
    {
        return $this->taskSendScheduled();
    }

    /**
     * @Route("/send/scheduled3", name="notification_send_scheduled3")
     * @Method("GET")
     */
    public function notificationSendScheduled3()
    {
        return $this->taskSendScheduled();
    }

    private function taskSendScheduled()
    {
        set_time_limit(0);
        ini_set('memory_limit', '2G');
        $apn = $this->get('coolway_app.apn');
        $em = $this->getDoctrine()->getManager();
        /**
         * Traigo las 25 notificaciones necesarias a ser procesadas
         */
        $notificationsScheduled = $em->getRepository('BackendBundle:NotificationSchedule')->findForProcess();
        $notificationRepo = $em->getRepository('BackendBundle:Notification');
        for ($i = 0; $i < count($notificationsScheduled); $i++) {
            $notificationsScheduled[$i]->setStatus(true);
            $em->persist($notificationsScheduled[$i]);
        }
        $em->flush();

        $count = 0;
        $apnStats = ["total" => count($notificationsScheduled), "successful" => 0, "failed" => 0];
        $notification = null;

        $feastRepo = $em->getRepository('BackendBundle:Feast');
        foreach ($notificationsScheduled as $scheduled) {
            $count++;
            //En caso de ser una notificación de artista el registro tendrá -1 como notificationId, leo el texto previamente almacenado
            if ($scheduled->getNotificationId() == -1) {
                $text = $scheduled->getText();
                $feast = $feastRepo->findOneById($scheduled->getFestId());
            } else {
                if (!$notification || $notification->getId() != $scheduled->getNotificationId()) {
                    $notification = $notificationRepo->findOneById($scheduled->getNotificationId());
                }
                $text = $notification->getText();
                $feast = $notification->getFeast();
            }

            //intento enviar la notificación, según este esquema se deben enviar una por una
            $stat = $apn->sendNotification(array($scheduled->getToken()),
                $text,
                5,
                $feast->getApnAppId(),
                'bingbong.aiff',
                $feast);

            if ($stat["successful"] > 0) {
                $apnStats["successful"] += 1;
                //por petición de mauro cuando la notificación es enviada se borra el registro de la tabla temporal
                //$em->remove($scheduled);
                //$em->flush();
            } else {
                $apnStats["failed"] += 1;
                //$scheduled->setStatus(false);
            }
            $em->remove($scheduled);
//            if ($count == 15) {
//                break;
//            }
        }

        $em->flush();

        return new Response('true');
    }
}