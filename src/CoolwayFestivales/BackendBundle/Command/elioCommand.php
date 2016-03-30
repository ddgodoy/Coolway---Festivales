<?php
/**
 * php app/console test:scheduler argument1 --env=dev
 */
namespace CoolwayFestivales\BackendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use CoolwayFestivales\BackendBundle\Entity\Sandbox;

class elioCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName       ('test:scheduler')
            ->setDescription('Scheduler test')
            ->addArgument   ('argument1', InputArgument::OPTIONAL, 'optional argument')
        ;
    }
    //
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $argument1 = $input->getArgument('argument1');
        $doctrine  = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();

        $oNow  = new \DateTime("now");
        $oTest = new Sandbox();

        $oTest->setReference(uniqid(''));
        $oTest->setCreated($oNow);

        $em->persist($oTest);
        $em->flush();

        $output->writeln($argument1);
    }

} // end class