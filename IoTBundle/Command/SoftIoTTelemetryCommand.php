<?php

namespace IoTBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


use IoT\Entity\Fields\RandomField;
use IoT\Entity\Fields\Field;
use IoT\Entity\ThingsBoard\REST\Device;
use IoT\Entity\ThingsBoard\REST\Login;
use IoT\Entity\Entity;
use IoT\Entity\Transports\HTTPServers;
use IoT\Task\RepeatableTask;
use IoT\Task\Delays\RandomDelay;


class SoftIoTTelemetryCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('SoftIoTTelemetry')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $argument = $input->getArgument('argument');

        if ($input->getOption('option')) {
            // ...
        }

        
        
        
        
        $logger = new ConsoleLogger($output);
        
        $login = new Login('test@ttt.com','12qwaszx');
        $loginToken= $login->run();
        
        
        $timeSeries= new \IoT\Entity\ThingsBoard\REST\TimeSeries('DEVICE','d0b441c0-d500-11e7-b6a7-134646a8fbab');
        $timeSeries->setUserAccessToken($loginToken);
        $timeSeries->keys = array('integerval','doubleval','stringval','booleanval','serialnumber');
        $timeSeries->startTs = strval(time()-(60*60*48)).'000'; //current - 2 days 
        $timeSeries->endTs = strval(time()).'000';
//        $timeSeries->agg = 'NONE';
//        $timeSeries->limit = 1;
        //interval is mandatory in version 1.3.1
//        $timeSeries->interval = 1000;
                
        $lastData= $timeSeries->run();
        
        
        
        
        
        $output->writeln('Command result.');
    }

}
