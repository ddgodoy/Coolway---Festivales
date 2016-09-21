<?php

namespace CoolwayFestivales\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\HttpFoundation\Response;
use CoolwayFestivales\BackendBundle\Entity\NotificationStats;

/**
 * Notification Schedule controller.
 *
 * @Route("/admin/notification-schedule")
 */
class NotificationScheduleController extends Controller
{
    /**
     * @Route("/send/scheduled/", name="notification_send_scheduled")
     * @Method("GET")
     */
    public function notificationSendScheduled()
    {
        set_time_limit(0);
        ini_set('memory_limit', '2G');
        $apn = $this->get('coolway_app.apn');
        $iosTokens = array();
        $em = $this->getDoctrine()->getManager();
        /**
         * Traigo las 25 notificaciones necesarias a ser procesadas
         */
        $notificationsScheduled = $em->getRepository('BackendBundle:NotificationSchedule')->findForProcess();
        $notificationRepo = $em->getRepository('BackendBundle:Notification');
        for ($i = 0; $i < count($notificationsScheduled); $i++) {
            $notificationsScheduled[$i]->setStatus(true);
            $em->persist($notificationsScheduled[$i]);
            $em->flush();
        }

        $count = 0;
        $apnStats = ["total" => count($notificationsScheduled), "successful" => 0, "failed" => 0];
        $notification = null;

        foreach ($notificationsScheduled as $scheduled) {
            $count++;
            $iosTokens[] = $scheduled->getToken();

            //En caso de ser una notificación de artista el registro tendrá -1 como notificationId, leo el texto previamente almacenado
            if ($scheduled->getNotificationId() == -1) {
                $text = $scheduled->getText();
            } else {
                if (!$notification || $notification->getId() != $scheduled->getNotificationId()) {
                    $notification = $notificationRepo->findOneById($scheduled->getNotificationId());
                }
                $text = $notification->getText();
            }

            //intento enviar la notificación, según este esquema se deben enviar una por una
            $stat = $apn->sendNotification($iosTokens,
                $text,
                5,
                $notification->getFeast()->getApnAppId(),
                'bingbong.aiff',
                $notification->getFeast());

            if ($scheduled->getNotificationId() != -1) {
                if ($stat["successful"] > 0) {
                    $notification->setDelivery(true);
                    $apnStats["successful"] += 1;
                    //por petición de mauro cuando la notificación es enviada se borra el registro de la tabla temporal
                    $em->remove($scheduled);
                    $em->flush();
                } else {
                    $notification->setDelivery(false);
                    $apnStats["failed"] += 1;
                }

                //Esta lógica estaba así en el controlador de notificaciones
                $notification->setSend(true);
                $em->persist($notification);
                $em->flush();
            }
        }

        //TODO: vi que en algunos puntos se implementaba una lógica para guardar estadísticas acerca de las notificaciones
        //sin embargo en la notificación de artistas no tiene dicha lógica y como aquí se envía una por una no se puede crear de manera sencilla
        //un registro consolidado
//        if (count($stat) > 0) {
//            $stats = new NotificationStats();
//            $stats->setNotification($notification);
//            $stats->setTotalDevices($apnStats["total"]);
//            $stats->setTotalAndroid(0);
//            $stats->setSuccessfulAndroid(0);
//            $stats->setFailedAndroid(0);
//            $stats->setTotalIOS($apnStats["total"]);
//            $stats->setSuccessfulIOS($apnStats["successful"]);
//            $stats->setFailedIOS($apnStats["failed"]);
//            $stats->setSent(new \DateTime("now"));
//            $em->persist($stats);
//
//        }

        return new Response('true');
    }
}