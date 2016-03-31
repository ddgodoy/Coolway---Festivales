<?php
/**
 * php app/console notification:send argument1 --env=dev
 */
namespace CoolwayFestivales\BackendBundle\Command;

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

        foreach ($notifications as $noti)
        {
            $em->getRepository('BackendBundle:Notification')->sendToMobile
            (
                $noti->getId(),
                $noti,
                $this->getContainer()->getParameter('kernel.root_dir')
            );
        }
    }

} // end class