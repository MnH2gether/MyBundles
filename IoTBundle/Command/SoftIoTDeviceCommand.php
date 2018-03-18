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
use IoT\Entity\Fields\FieldTypes;

use IoT\Tools\TreeNodeEx;
use IoT\Tools\Commander;

use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\StreamOutput;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;



class SoftIoTDeviceCommand extends ContainerAwareCommand
{
    
    protected $thingsBoardDeviceAccessToken = '';
    
    /**
     *
     * @var string Path to root dir where will store *.cfg files and log of work 
     */
    protected $iotStorePath = '';
    
    /**
     *
     * @var Symfony\Component\Console\Output\OutputInterface Instance 
     */
    protected $outputLogStream = null; /* maybe stream some psrLog simple instance */
    
    /**
     *
     * @var string File name of current log file on disk.
     * Because $outputLogStream have not information about related file name on disk this property contains it.
     */
    protected $logFileName = '';
    
    protected $logFileHandle = null;
    
    /**
     *
     * @var Psr\Log\LoggerInterface Instance
     */
    protected $logger = null;
    
    /**
     * @var InputInterface
     */
    private $input = null;
    
    /**
     *
     * @var OutputInterface 
     */
    private $output = null;
    
    /**
     *
     * @var QuestionHelper
     */
    private $helper = null;
    
    
    public function __destruct() {
        if(!empty($this->logFileHandle) && is_resource($this->logFileHandle)) fclose($this->logFileHandle);
    }
    
    /**
     * 
     * @return SoftIoTDeviceCommand Itself
     * @throws Exception
     */
    protected function testDir($subdir = '') {
        $storePath = $this->iotStorePath."$subdir";
        
        if ( !file_exists($storePath) ){
            mkdir($storePath);
        } else if ( !is_dir($storePath) ) {
            throw new Exception('File \''.$storePath.'\' exists instead dirictory with same name.');
        }
        return($storePath);
    }

    /**
     * 
     * @param string $token
     * @return boolean true if token is right or false otherwise.
     */
    protected function setThingsBoardDeviceAccessToken($token){
        $this->thingsBoardDeviceAccessToken = '';
        $token = "$token";
        if(!empty($token)) $this->thingsBoardDeviceAccessToken = $token;
        
        return(!empty($this->thingsBoardDeviceAccessToken));
    }
    
    
    protected function getConfigFileName(){
        $result = '';
        if( !empty(trim($this->thingsBoardDeviceAccessToken)) ){
            $result= realpath($this->iotStorePath).'/./'.trim($this->thingsBoardDeviceAccessToken).'.cfg';
        }
        return($result);
    }
    
    protected function getConfigContent(){
        $result = '';
        $fname = $this->getConfigFileName();
        if ( $fname !== '' && file_exists($fname) && is_file($fname) ) {
            $result = file_get_contents($fname);
        }
        return($result);
    }
    
    
    protected function createConfigFile($content){
        $result = false;
        //need be carefull on time of change day
        $storeDir = $this->testDir(''); 
        $fname = $this->getConfigFileName();
        if ( empty(trim($content) ) ) {
            if (file_exists($fname) && is_file($fname)) unlink($fname);
        } else {
            $result = file_put_contents($fname, $content);
        }
        return($result);
    } 
    
    protected function getLogFileName(){
        return($this->logFileName);
    } 
    
    protected function getLogStream(){
        return($this->outputLogStream);
    }
    
    protected function createLogger(){
        //need be carefull on time of change day
        $logDir = $this->testDir('/log');
        
        $this->logFileName= $logDir.'/'.$this->thingsBoardDeviceAccessToken.date('Ymd').'.log';
        $this->logFileHandle = fopen($this->getLogFileName(), 'ab', false);
        if ( $this->logFileHandle !== false ){
            $this->outputLogStream =  new StreamOutput($this->logFileHandle,OutputInterface::VERBOSITY_VERY_VERBOSE);
            $this->logger = new ConsoleLogger($this->getLogStream() /*, array('info' => 32) */);
        } else throw  new \RuntimeException ('Can\'t create log file \''.$this->getLogFileName().'\' for device \''.$this->thingsBoardDeviceAccessToken.'\'');
        return($this);
    } 
    
    
    protected function initialize(InputInterface $input, OutputInterface $output) {
        parent::initialize($input, $output);
        
        $this->input = $input;
        $this->output = $output;
        
        $this->iotStorePath = $this->getContainer()->getParameter('kernel.root_dir').'/../var/'.str_replace(':','', ucwords($this->getName(), ':'));
        $this->testDir();
    }
    
    protected function configure()
    {
        
        $this
            ->setName('iot:device:repeatable-random-values')
            ->setDescription('Run software emulation of Internet of Things device. On this time realised only support the REST API of ThingsBoard <https://thingsboard.io/docs/reference/http-api/#telemetry-upload-api>.')
            ->addArgument('deviceAccessToken', InputArgument::OPTIONAL, 'Device\'s access token from dashboard of ThingsBoard <https://thingsboard.io/docs/getting-started-guides/helloworld/#manage-device-credentials>.' /*, 'uFdYmWuHa2c8fgh3jRQT' */)
            ->addOption('host', null, InputOption::VALUE_OPTIONAL, 'Host (ip address or dns name) where deployed ThingsBoard server')
            ->addOption('port', null, InputOption::VALUE_OPTIONAL, 'Port where binded ThingsBoard server on.')
                
            ->setHelp(<<<EOT
The <info>%command.name%</info> command creates the default connections database:
    <info>php %command.full_name%</info>
You can also optionally specify the name of a connection to create the database for:
    <info>php %command.full_name% --connection=default</info>
EOT
        );
    }


    /**
     * 
     * @param TreeNodeEx $action
     * @param Commander $commander
     * @return type
     */
    protected function isLimitReached(TreeNodeEx $action, Commander $commander, TreeNodeEx $jumpToAction = null){
        $result = false;
        
        $actionName = $action->getNodePath();
        $data = $commander->getData();

        if ( isset($data[$actionName]['attempts']) && $data[$actionName]['attempts'] > 3 ){
            if ( is_null($jumpToAction) ) {
                $jumpToAction = $action;
            }
            
            $this->output->writeln("Limit of attempts were reached:");
            $question = new ChoiceQuestion("Do you want stop?",array('No','Yes'),'No');
            $answer = $this->helper->ask($this->input,$this->output,$question);
            if($answer === 'No') {
                $commander->continueFrom($jumpToAction);
                $data[$actionName]['attempts']=0;
            } else {
                $commander->setStop();
            }
            $result= true;
        } 
        return($result);
    }

    protected function attempsIncrement(TreeNodeEx $action, Commander $commander){
        $actionName = $action->getNodePath();
        $data = $commander->getData();

        if ( !isset($data[$actionName]) ) { 
            $data[$actionName] = array('attempts'=>0); 
        }
        $data[$actionName] ['attempts']++;
    }


    /**
     * Action which questioning about Do You Want Stop the continue this action after 4 attempts.
     * 
     * @param callable $callback
     * @param TreeNodeEx $action
     * @param Commander $commander
     * @param TreeNodeEx $jumpToAction
     * @return  null
     */
    protected function questioningDYWSAction(callable $callback, TreeNodeEx $action, Commander $commander,  TreeNodeEx $jumpToAction = null){
        $isReached = $this->isLimitReached($action, $commander, $jumpToAction);
        if ($isReached === true) {
            return;
        }
        
        call_user_func_array($callback, array($action, $commander));

        $this->attempsIncrement($action, $commander);
    }
    
    private function getIntendedData(TreeNodeEx $action, Commander $commander, $testName){
        $mainChild = $action->getFirst();
        
        if ( !is_null($mainChild->getParent()) && !is_null($mainChild->getParent()->getChild()) &&  
             $mainChild->getParent()->getChild() === $mainChild) $mainChild = $mainChild->getParent();
        
        if($mainChild->getName() !== $testName) throw new \Exception ('Not appropriate firstParent node for \''.$action->getName().'\'');
        return($commander->getDataBy($mainChild));
    }
    
    protected function addSameFieldAttributeCommands(Commander $commander, $partName = 'Field'){
        $commander
        ->addNext(str_replace('%', ucfirst($partName), 'prompt%Name'),function(TreeNodeEx $action, Commander $commander) use ($partName){
            $items = $this->getIntendedData($action, $commander,str_replace('%', ucfirst($partName), 'init%s'));

            $item = new RandomField();
            $question= new Question("Name (only [a-z] allowed): \r\n");
            $token= $this->helper->ask($this->input,$this->output,$question);
            $item->setName($token);
            $this->output->writeln('Chosen/pointed: '.$item->getName() );

            $items[] = $item;
        })
        ->addNext(str_replace('%', ucfirst($partName), 'prompt%Type'),function(TreeNodeEx $action, Commander $commander) use ($partName){
            $items = $this->getIntendedData($action, $commander,str_replace('%', ucfirst($partName), 'init%s'));
            $item = $items[count($items)-1];
                
            $question = new ChoiceQuestion("Type: ", array_merge(array('randomly'),FieldTypes::IoTEntityFieldTypes),'randomly');
            $answer = $this->helper->ask($this->input, $this->output, $question);
            if($answer === 'randomly') $answer = '';
            $item->setType($answer);
            $this->output->writeln('Chosen/pointed: '.$item->getType() );
        })
        ->addNext(str_replace('%', ucfirst($partName), 'prompt%Value'),function(TreeNodeEx $action, Commander $commander) use ($partName){
            $items = $this->getIntendedData($action, $commander,str_replace('%', ucfirst($partName), 'init%s'));
            $item = $items[count($items)-1];
            
            $question = new Question("Value: ");
            $value= $this->helper->ask($this->input,$this->output,$question);
            $item->setValue(FieldTypes::strToTypedValue($item->getType(),$value));
            $this->output->writeln('Chosen/pointed: '.$item->getValue() );
        })
        ->addNext(str_replace('%', ucfirst($partName), 'print%Info'),function(TreeNodeEx $action, Commander $commander) use ($partName){
            $items = $this->getIntendedData($action, $commander,str_replace('%', ucfirst($partName), 'init%s'));
            $item = $items[count($items)-1];
            $this->output->writeln(str_replace('%', strtolower($partName), 'Was added %: Name->\'').$item->getName().'\' of Type->\''.$item->getType().'\' and Value->\''.$item->getValue().'\'' );
        })
        ->addNext('questionAddMore',function(TreeNodeEx $action, Commander $commander) use ($partName){
            $question = new ChoiceQuestion(str_replace('%', strtolower($partName), "Do you want create next %?"),array('No','Yes'),'No');
            $answer = $this->helper->ask($this->input, $this->output, $question); 
            if ( $answer === 'Yes' ) {
                $commander->continueFrom($action->getClosestByName(str_replace('%', ucfirst($partName), 'prompt%Name')));
            }
        });
    }
    
    
    /**
     * This create child sub branch for current chain and returns new next node
     * for current chain that was created as point of entering for sub branch.
     * But current node still is last added node in subtree that created by
     * addFieldsSubtreeActions(). You can be invoke $commander->setCurrent($returnedValue)
     * immediately after or in any other places for prolongation main/current chain.
     * 
     * @param Commander $commander
     * @return TreeNodeEx Node that was created as next for current chain. 
     */
    protected function addFieldsSubtreeActions(Commander $commander){
        $commander->addNext('initFields',function(TreeNodeEx $action, Commander $commander){
            $data= $commander->getDataBy($action);
        });
        $initFields = $commander->getCurrent();

        $commander
        ->addChild('messageAboutCreationFieldsOfDevice', function(TreeNodeEx $action, Commander $commander) {
            $this->output->writeln('Creation fields of device:');
        })
        ->addNext('messageAboutSetUpFieldsOfDevice',function(TreeNodeEx $action, Commander $commander){
            $this->output->writeln("Now, we need set up some parameters of Field which will be sent to server (Name, Type and Value)\r\n".
                        "if answered value is empty then it will generate automaticaly:");
        });
        
        $this->addSameFieldAttributeCommands($commander, 'Field');
        
        return($initFields);
    }
    
    /**
     * This create child sub branch for current chain and returns new next node
     * for current chain that was created as point of entering for sub branch.
     * But current node still is last added node in subtree that created by
     * addAttributesSubtreeActions(). You can be invoke $commander->setCurrent($returnedValue)
     * immediately after or in any other places for prolongation main/current chain.
     * 
     * @param Commander $commander
     * @return TreeNodeEx Node that was created as next for current chain. 
     */
    protected function addAttributesSubtreeActions(Commander $commander){
        $commander->addNext('initAttributes',function(TreeNodeEx $action, Commander $commander){
            $question = new ChoiceQuestion("Do you want create attributes at all?",array('No','Yes'),'No');
            $answer = $this->helper->ask($this->input, $this->output, $question); 
            if ( $answer === 'No' ) {
                $fullNodeName = $action->getNodePath().$action->getSeparatorOfNesting().'dumbAttributeNodeForNormalExit';
                $lastNodes = $action->getNodesByName($fullNodeName);
                if(isset($lastNodes[$fullNodeName])) $commander->continueFrom($lastNodes[$fullNodeName]);
                else throw new \Exception ('Can\'t at \''.$fullNodeName.'\' point of execution.');
            }
        });
        $initAttributes = $commander->getCurrent();

        $commander
        ->addChild('messageAboutCreationAttributesOfDevice', function(TreeNodeEx $action, Commander $commander) {
            $this->output->writeln('Creation attributes of device:');
        })
        ->addNext('messageAboutSetUpAttributesOfDevice',function(TreeNodeEx $action, Commander $commander){
            $this->output->writeln("Now, we need set up some parameters of Attribute which will be sent to server (Name, Type and Value)\r\n".
                        "if answered value is empty then it will generate automaticaly:");
        });
        
        $this->addSameFieldAttributeCommands($commander, 'Attribute');
        
        $commander->addNext('dumbAttributeNodeForNormalExit',function(TreeNodeEx $action, Commander $commander){
        });
        
        return($initAttributes);
    }

    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->helper = $this->getHelper('question');
        $softDevice = null;
        
        $commander = new Commander();
        $commander
        ->addNext('getAccessToken', function(TreeNodeEx $action, Commander $commander) {
            $this->questioningDYWSAction(
                function(TreeNodeEx $action, Commander $commander){
                    if ( !$this->setThingsBoardDeviceAccessToken( $this->input->getArgument('deviceAccessToken') ) ){
                        $question= new Question("Device's access token: \r\n");
                        $token= $this->helper->ask($this->input,$this->output,$question);
                        if ( !$this->setThingsBoardDeviceAccessToken($token) ) $commander->continueFrom($action);
                    }       
                }, $action, $commander);
        })
        ->addNext('createInstanceThingsBoardDeviceWithGivenAccessToken', function(TreeNodeEx $action, Commander $commander) use (&$softDevice) {
            //My test string
            //$this->thingsBoardDeviceAccessToken =  'uFdYmWuHa2c8fgh3jRQT';
            
            $arrNodes = array('initFields'=>null, 'initAttributes'=>null);
            foreach($arrNodes as $nodeName=>&$node){
                $nodes= $action->getNodesByName($nodeName);
                if( count($nodes) > 0 ){
                    $node = current($nodes);
                }
            }
            
            $createNew = true; 
            $cfgContent = $this->getConfigContent();
            if ($cfgContent !== ''){
                $tDevice = unserialize($cfgContent);
                $fields = $commander->getDataBy($arrNodes['initFields']);
                $fields->exchangeArray($tDevice->getFields());
                $attributes = $commander->getDataBy($arrNodes['initAttributes']);
                $attributes->exchangeArray($tDevice->getAttributes());
                
                $this->output->writeln('Last used device settings are: ');
                $summaryNodes = $action->getNodesByName('printSummary');
                if( count($summaryNodes) > 0 ){
                    $summaryNode = current($summaryNodes);
                    call_user_func($summaryNode->getData(), $summaryNode, $commander);
                }
                $question = new ChoiceQuestion("Do you want to load these?",array('Yes','No'),'Yes');
                $answer = $this->helper->ask($this->input, $this->output, $question); 
                if ( $answer === 'Yes' ) {
                    $softDevice = $tDevice;
                    $createNew = false;
                    
                    $promptTBServerNodes = $action->getNodesByName('promptThingsBoardServerHostName');
                    if( count($promptTBServerNodes) > 0 ){
                        $commander->continueFrom(current($promptTBServerNodes));
                    }   
                } else {
                    $fields->exchangeArray(array());
                    $attributes->exchangeArray(array());
                }
            }
            
            
            
            
            if( $createNew ) {
                $softDevice = new Device($this->thingsBoardDeviceAccessToken);
            }
            $this->createLogger();
        });

        $mainFieldsSubtreeNode = $this->addFieldsSubtreeActions($commander);
        $commander->setCurrent($mainFieldsSubtreeNode);
        
        $mainAttributesSubtreeNode = $this->addAttributesSubtreeActions($commander);
        $commander->setCurrent($mainAttributesSubtreeNode)
                
        ->addNext('printSummary', function(TreeNodeEx $action, Commander $commander) use ($mainFieldsSubtreeNode, $mainAttributesSubtreeNode) {
            $arr = array('Field'=>$mainFieldsSubtreeNode, 'Attribute'=>$mainAttributesSubtreeNode);
            foreach($arr as $dataName=>$mainDataNode){
                $items = $commander->getDataBy($mainDataNode);
                $this->output->writeln(str_replace('%', ucfirst($dataName), '%s list: '));
                if ( count($items) > 0 ){
                    foreach ($items as $item) {            
                        $this->output->writeln(str_replace('%', strtolower($dataName), '- %: Name->\'').$item->getName().'\' of Type->\''.$item->getType().'\' and Value->\''.$item->getValue().'\'' );
                    }
                } else $this->output->writeln("- is empty");
            }
        })
        ->addNext('promptThingsBoardServerHostName', function(TreeNodeEx $action, Commander $commander) use (&$softDevice){
            $host= trim($this->input->getOption('host'));
            if ( $host === '' ){
                $question= new Question("Type the ThingsBoard's server host name [".$softDevice->getUrl()->getHost()."]: \r\n", $softDevice->getUrl()->getHost());
                $host= $this->helper->ask($this->input,$this->output,$question);
            }
            $softDevice->getUrl()->setHost($host);
            $this->output->writeln('Chosen the ThingsBoard\'s server host name -> \''.$softDevice->getUrl()->getHost().'\'');
        })
        ->addNext('promptThingsBoardServerPort', function(TreeNodeEx $action, Commander $commander) use (&$softDevice){
            $port= trim($this->input->getOption('port'));
            if ( $port === '' ){
                $question= new Question("Type the ThingsBoard's server port [".$softDevice->getUrl()->getPort()."]: \r\n", $softDevice->getUrl()->getPort());
                $port= $this->helper->ask($this->input,$this->output,$question);
            }
            $softDevice->getUrl()->setPort($port);
            $this->output->writeln('Chosen the ThingsBoard\'s server port -> \''.$softDevice->getUrl()->getPort().'\'');
        })
        ->addNext('initDeviceFields', function(TreeNodeEx $action, Commander $commander) use (&$softDevice, $mainFieldsSubtreeNode){
            $fields = $commander->getDataBy($mainFieldsSubtreeNode);
            foreach ($fields as $field) {
                $softDevice->setField($field);
            }
        })
        ->addNext('initDeviceAttributes', function(TreeNodeEx $action, Commander $commander) use (&$softDevice, $mainAttributesSubtreeNode){
            $attributes = $commander->getDataBy($mainAttributesSubtreeNode);
            foreach ($attributes as $attribute) {
                $softDevice->setAttribute($attribute);
            }
        })
        ->addNext('promptStoreSettingsToFile', function(TreeNodeEx $action, Commander $commander) use (&$softDevice){
            $this->output->writeln('Current settings of device identified by AccessToken -> \''.$softDevice->getAccessToken().'\' will be stored for using in the future.');
        })
        ->addNext('storeSettingsIntoFile', function(TreeNodeEx $action, Commander $commander) use (&$softDevice){
            $serialized = serialize($softDevice);
            $isStored = $this->createConfigFile($serialized);
            $this->output->writeln('Current device setttings were '.($isStored?'successfully':'not').' stored into \''.$this->getConfigFileName().'\' file.');
        });
        
        
        $commander->getMain()->printTree();
        
        $commander->run();
        

        $softDevice->setOnError(
            function (Device $device) {
                /**
                 * @var \IoT\Entity\Transports\HTTPServers\ThingsBoard
                 */    
                $transport = $device->getTransport();
                if ( $transport->isDebugModeOn() ) $this->logger->error($transport->getDebugInfo());
            }
        );
        
        $i=0;
        $task = new RepeatableTask(
                function ($task, $logger) use (&$i,$softDevice,$commander,$output) {
                    $result= $softDevice->run();
                    
                    $nodes= $commander->getMain()->getNodesByName('printSummary');
                    if ( count($nodes) > 0 ) {
                        $node = current($nodes);
                        $callback = $node->getData();
                        if ( is_callable($callback) ) {
                            $output->writeln('Next data were sent on server at '.date('Y-m-d H:i:s').' '.($result?'successful':'but failure happened').':');
                            call_user_func($callback, $node, $commander);
                        }
                    }
                    
                    foreach($softDevice->getFields() as $field) { 
                        $field->setValue();      
                    }
// You can stop execution after $i steps if uncomment next lines and change $i on appropriate value.                 
//                    if( $i >= 3 ) $task->stop();
//                    $i++;
                    return($result);
                }, $this->logger, new RandomDelay(10000,60000)
        );

        
       /**
        * @var Psr\Log\LoggerInterface Description
        */
        $monoLog = $this->getContainer()->get('logger');
        $monoLog->info('Software IoT device started. Detailed log see in \''.$this->getLogFileName().'\'');
        $task->run();
        $monoLog->info('Software IoT device done. Detailed log see in \''.$this->getLogFileName().'\'');
    }

}
