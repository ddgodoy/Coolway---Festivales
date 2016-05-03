<?php
/**
 * php app/console notification:send argument1 --env=dev
 */
namespace CoolwayFestivales\BackendBundle\Command;

use CoolwayFestivales\BackendBundle\Entity\NotificationStats;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class notificationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName       ('notification:send')
            ->setDescription('Envio de notificaciones')
            ->addArgument   ('argument1', InputArgument::OPTIONAL, 'optional argument')
        ;
    }
    //
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        $notifications = $doctrine->getRepository('BackendBundle:Notification')->findForBatch();

        foreach ($notifications as $entity)
        {
            $notification = $em->getRepository('BackendBundle:Notification')->findById($entity->getId());
            $devices = $em->getRepository('SafetyBundle:Device')->findBy(array('feast' => $entity->getFeast()->getId()));
            $androidTokens = array();
            $iosTokens = array();

            foreach($devices as $device) {
                if($device->getOs() == 1)
                    $iosTokens[] = $device->getToken();
                else
                    $androidTokens[] = $device->getToken();
            }

            if ($notification) {
                $gcmStats = array();
                $apnStats = array();

                if (sizeof($androidTokens) > 0) {
                    $gcm = $this->getContainer()->get('coolway_app.gcm');
                    $gcmStats = $gcm->sendNotification($androidTokens,
                        $notification->getName(),
                        $notification->getText(),
                        'admin-notification',
                        'com.gravedad.lesarts',
                        false,
                        600,
                        false);
                }

                if (sizeof($iosTokens) > 0) {
                    $apn = $this->get('coolway_app.apn');
                    $apnStats = $apn->sendNotification($iosTokens,
                        $notification->getText(),
                        5,
                        'com.gravedad.lesarts',
                        'bingbong.aiff');
                }

                if (count($apnStats) > 0 || count($gcmStats) > 0) {
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
                } else
                    $notification->setDelivery(false);

                $notification->setSend(true);
                $em->persist($notification);
                $em->flush();
            }
        }
    }

} // end class