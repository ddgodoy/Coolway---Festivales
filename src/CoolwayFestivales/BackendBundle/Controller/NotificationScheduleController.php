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
        $start = microtime(true);
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
        }
        $em->flush();

        $count = 0;
        $apnStats = ["total" => count($notificationsScheduled), "successful" => 0, "failed" => 0];
        $notification = null;

        foreach ($notificationsScheduled as $scheduled) {
            $iosTokens[] = $scheduled->getToken();
            $count++;
            //En caso de ser una notificación de artista el registro tendrá -1 como notificationId, leo el texto previamente almacenado
            if ($scheduled->getNotificationId() == -1) {
                $text = $scheduled->getText();
            } else {
                if (!$notification || $notification->getId() != $scheduled->getNotificationId()) {
                    $notification = $notificationRepo->findOneById($scheduled->getNotificationId());
                }
                $text = $notification->getText();
            }
            echo '<pre>Texto: ' . var_dump($text) . '</pre>';
            //intento enviar la notificación, según este esquema se deben enviar una por una
            $stat = $apn->sendNotification($iosTokens,
                $text,
                5,
                $notification->getFeast()->getApnAppId(),
                'bingbong.aiff',
                $notification->getFeast());

            echo '<pre>' . var_dump($stat) . '</pre>';

            if ($stat["successful"] > 0) {
                $apnStats["successful"] += 1;
                //por petición de mauro cuando la notificación es enviada se borra el registro de la tabla temporal
                $em->remove($scheduled);
                //$em->flush();
            } else {
                $apnStats["failed"] += 1;
                $scheduled->setStatus(false);
            }
            if ($count == 15) {
                break;
            }
        }

        $em->flush();
        $time_elapsed_secs = microtime(true) - $start;
        echo '<pre>' . $time_elapsed_secs . '</pre>';

        return new Response('true');
    }
}