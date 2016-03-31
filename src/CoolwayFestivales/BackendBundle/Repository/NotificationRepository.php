<?php

namespace CoolwayFestivales\BackendBundle\Repository;

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
        $dNow = date('Y-m-d');
        $tNow = date('H:i').':00';

        $q = $this->getEntityManager()->createQuery
        (
            "SELECT n FROM BackendBundle:Notification n
			WHERE n.send = 0 AND n.date IS NOT NULL AND n.date = '$dNow' AND n.time = '$tNow'
			ORDER BY n.id ASC"
        );
        return $q->getResult();
    }
    //
    public function sendToMobile($id, $entity, $kernel_dir)
    {
        $em  = $this->getEntityManager();
        $ids = array(
            'Android'=> array(),
            'IOS'    => array()
        );
        $notif = $em->getRepository('BackendBundle:Notification')->find($id);
        $users = $em->getRepository('SafetyBundle:User')->findUsersInFestival($entity->getFeast()->getId());

        foreach($users as $u) {
            $ids[$u->getOs()][] = $u->getNotificationId();
        }
        if (count($ids['Android']) || count($ids['IOS']))
        {
            if ($notif)
            {
                $title   = $notif->getName();
                $message = $notif->getText();
                $url     = 'https://android.googleapis.com/gcm/send';
                $fields  = array(
                    "data" => array(
                        'title'   => $title,
                        'message' => $message,
                    ),
                    "registration_ids" => $ids['Android']
                );
                $headers = array(
                    'Authorization: key=AIzaSyCFpBmNym9kaRPoUA-ZKogSk-QZzvLhlfc',
                    'Content-Type: application/json'
                );
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                curl_exec  ($ch);
                curl_close ($ch);

                $fields = array(
                    'aps' => array(
                        'alert' => $message,
                        'title' => $title,
                        'sound' => 'bingbong.aiff'
                    )
                );
                $payload = json_encode($fields);
                $passphrase = 'iY88bR62';

                foreach ($ids['IOS'] as $deviceToken)
                {
                    $ctx = stream_context_create();
                    stream_context_set_option($ctx, 'ssl', 'local_cert', $kernel_dir.'/../mobile/certs/aps_production.pem');
                    stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
                    $fp = stream_socket_client(
                        'ssl://gateway.push.apple.com:2195', $err,
                        $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx
                    );
                    $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

                    fwrite($fp, $msg, strlen($msg));
                    fclose($fp);
                }
                $notif->setSend(1);
                $em->persist($notif);
                $em->flush();
            }
        }
    }

} // end class