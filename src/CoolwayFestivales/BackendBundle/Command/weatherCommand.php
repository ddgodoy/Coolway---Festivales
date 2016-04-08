<?php
/**
 * php app/console weather:update argument1 --env=dev
 */
namespace CoolwayFestivales\BackendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class weatherCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName       ('weather:update')
            ->setDescription('Actualizacion del clima para los proximos 10 dias')
            ->addArgument   ('argument1', InputArgument::OPTIONAL, 'optional argument')
        ;
    }
    //
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine   = $this->getContainer()->get('doctrine');
        $festivales = $doctrine->getRepository('BackendBundle:Feast')->findAll();

        foreach ($festivales as $value)
        {
            if (!empty($value->getLatitude()) && !empty($value->getLongitude()))
            {
                $doctrine->getRepository('BackendBundle:Weather')->setForecastInfo
                (
                    $value,
                    $value->getLatitude(),
                    $value->getLongitude()
                );
            }
        }
    }

} // end class