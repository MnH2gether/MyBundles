<?php

/*
 * Copyright (c) 2017, Semyon Mamonov <semyon.mamonov@gmail.com>.
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 * 
 *  * Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 
 *  * Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in
 *    the documentation and/or other materials provided with the
 *    distribution.
 * 
 *  * Neither the name of Semyon Mamonov nor the names of his
 *    contributors may be used to endorse or promote products derived
 *    from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace IoT\Tools;


//
//spl_autoload_register(function($class){
//    
//    $fileName = __DIR__.'/../../'.$class.'.php';
//    $fileName = realpath($fileName);
//    if ( file_exists($fileName) && is_file($fileName) ){
//        require_once($fileName);
//    }
//    
//});
//
//
//
//
//
//
use IoT\Entity\IRunnable;

/**
 * Generated: Dec 10, 2017 6:40:51 AM
 * 
 * Description of Commander
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class Commander implements IRunnable {
    //put your code here
    
    /**
     * Main TreeNodeEx instance
     * 
     * @var TreeNodeEx 
     */
    protected $main = null;
    
    /**
     * Reference at current TreeNodeEx 
     * 
     * @var TreeNodeEx
     */
    protected $current = null;
    
    /**
     * Use only in 'run' method as start node for repeat actions.
     * 
     * @var TreeNodeEx 
     */
    protected $continueFrom = null;
    
    /**
     * Common data for all loop. they can be add, remove, change and etc.
     * 
     * @var \ArrayObject
     */
    protected $data = null;
    
    /**
     * Flag that pointing to loop, it must be terminated or not. 
     *  
     * @var boolean
     */
    protected $stop = false;
    
    
    
    public function __construct() {
        $this->main = new TreeNodeEx('MAIN',array($this,'defaultMainAction'));
        $this->current = $this->main;
        $this->data = new \ArrayObject();
    }

    /**
     * 
     * @param TreeNodeEx $action
     * @param Commander $commander
     */
    protected function defaultMainAction(TreeNodeEx $action,  Commander $commander){
        
    }
    
    
    /**
     * Set flag into turned to on state.
     * 
     * @param boolean $stop True if need to stop run loop, otherwise false.
     * @return Commander
     */
    public function setStop($stop = true){
        $this->stop = boolval($stop);
        return($this);
    }
    
    /**
     * 
     * @return boolean True, if state was turned into On state.
     */
    public function isStopped(){
        return( $this->stop === true );
    }
    
    
    /**
     * 
     * @return array Old data.
     */
    public function clearData(){
        return( $this->getData()->exchangeArray(array()) );
    }
    
    
    /**
     * Returns reference on global for this Commander instance data storage.
     * @return \ArrayObject
     */
    public function getData(){
        return($this->data);
    }
    
    /**
     * Returns reference on data storage that intended for certain $node.
     * The storage always is instance of \ArrayObject that is why you can change
     * data in any places of code and it will be seen in other places of code
     * immediately.
     * 
     * @param TreeNodeEx $node
     * @return mixed
     */
    public function getDataBy(TreeNodeEx $node){
        $result = null;
        if ( $this->isInsideTree($node) ) {
            $data = $this->getData();
            $nodePath = $node->getNodePath();
            if ( !isset($data["$nodePath"]) ) $data["$nodePath"] = new \ArrayObject();
            $result = $data["$nodePath"];
        }
        return($result);
    }
    
    
    /**
     * 
     * @return TreeNodeEx
     */
    public function getMain(){
        return($this->main);
    }

    /**
     * 
     * @return TreeNodeEx
     */
    public function getCurrent(){
        return($this->current);
    }

    /**
     * 
     * @param TreeNodeEx $node
     * @return Boolean
     */
    protected function isInsideTree(TreeNodeEx $node){
        $result = false;
        $actions= $this->_getFlatRepresentationOfTree();
        if(!is_null($node) && count($actions) > 0 && in_array($node, $actions, true)){
            $result= true;
        } else trigger_error('Action with name \''.$node->getNodePath().'\' not in command\'s tree.', E_USER_WARNING);
        return($result);
    }
    
    
    /**
     * 
     * @param TreeNodeEx $node
     * @return Commander
     */
    public function setCurrent(TreeNodeEx $node){
        if ( $this->isInsideTree($node) ) $this->current = $node;
        return($this);
    }
    
    
    /**
     * 
     */
    public function setCurrentByName($nodePath, $exactly = false){
        $nodes = $this->getMain()->getNodesByName($nodePath);
        if( count($nodes) > 0 ){
            if( count($nodes) > 1 ) {
                trigger_error('Actions with name similar to \''.$nodePath.'\' was found more than one.', E_USER_NOTICE);
            }
            
            if( !$exactly ) $nodePath = key($nodes);
            if( isset($nodes["$nodePath"]) ) $this->current = $nodes["$nodePath"];
             else trigger_error('Actions with name \''.$nodePath.'\' was not found. But found \''.key($nodes).'\'', E_USER_NOTICE);
        } else trigger_error ('Action with name similar to \''.$nodePath.'\' was not found.', E_USER_NOTICE);
        return($this);
    }
    
    /**
     * 
     * 
     * @param string $actionName
     * @param callable $action Action function/method must be compatible with next mask - function(TreeNodeEx $action, Command $this).
     * @return Commander Itself 
     */
    public function addChild($actionName, callable $action){
        $this->current = $this->getCurrent()->setChild(new TreeNodeEx($actionName, $action));
        return($this);
    }
    
    /**
     * 
     * @param string $actionName
     * @param callable $action Action function/method must be compatible with next mask - function(TreeNodeEx $action, Command $this).
     * @return Commander Itself
     */
    public function addNext($actionName, callable $action){
        $this->current = $this->getCurrent()->setNext(new TreeNodeEx($actionName, $action));
        return($this);
    }
    
    
    private function _getFlatRepresentationOfTree(){
        $result = array();
        $callback = function ($node) use (&$result) {
            $result[]= $node;
        };
        $this->getMain()->captureWalk($callback);
        return($result);
    }
    
    
    public function continueFrom(TreeNodeEx $node){
        $this->continueFrom = $node;
    }
    
    /**
     * 
     * 
     * 
     * @throws Exception
     */
    public function run() {
        $this->setStop(false);
        $oldData= $this->clearData();
        $actions = $this->_getFlatRepresentationOfTree();
        $cIdx = 0;
        
        while ( $cIdx < count($actions) && !$this->isStopped() ){
            if(!is_null($this->continueFrom) ) {
               if( false !== $fIdx = array_search($this->continueFrom, $actions, true)){
                   $cIdx = $fIdx;
                   $this->continueFrom = null;
               } else throw new Exception ('In process the Command execution was set action \''.$this->continueFrom->getNodePath().'\' for repeate but action does not exist in list current command\'s actions.');
            } 
            $cNode = $actions[$cIdx];
            call_user_func($cNode->getData(), $cNode, $this);
            if(is_null($this->continueFrom) ) {
                $cIdx++;
            }
        }
    }
    
}





//
//$callBack = function(TreeNodeEx $node, Commander $commander){
//    $hash = spl_object_hash($node);
//    $ns= $node->getNodePath();
//    print($ns.':'.$hash."\r\n");
//    $commander->getData()[$hash]= $ns;
//};
//
//$commander= new Commander();
//$commander->addChild('Child ActionNode of MAIN',$callBack)
//        ->addNext('Next ActionNode for Child of MAIN',$callBack)
//        ->addChild('Child ActionNode for MAIN_Child_Next of MAIN',$callBack)
//        ->addNext('Next ActionNode for MAIN_Child_Next_Child of MAIN',$callBack)
//        ->addNext('Next ActionNode of Next for Child of MAIN',$callBack);
//$commander->setCurrent($commander->getMain())->addNext('Next for MAIN',$callBack);
//
//$commander->setCurrentByName('Next ActionNode for MAIN_Child_Next_Child of MAIN',true);
//
//print($commander->getCurrent()->getNodePath()."\r\n");
//
//
//$commander->run();
//
//print_r($commander->getData());
//
//$commander->getMain()->printTree();