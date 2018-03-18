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


use IoT\Entity\IRunnable;
use IoT\Tools\Data\ArrayDataStorage;


/**
 * Generated: Dec 19, 2017 9:47:32 PM
 * 
 * Description of ActionNode
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class ActionNode extends TreeNodeEx  implements INamespaced, IRunnable {
    //put your code here
    
    /**
     * Callback will get two input parameters - first is input data and second is itself node.
     * 
     * @param string $actionName Name of Action without spaces..... 
     * @param \IoT\Tools\callable $action is callback that will take two parameters - 
     * first is input data passed from supper command (if this command is child), if
     * no data from super command then will passed null and second is reference
     * on $this (current) ActionNode.
     */
    public function __construct($actionName, callable $action ) {
        parent::__construct($actionName, $action);
    }
    
    public function __destruct() {
        ArrayDataStorage::instance()->unsetData($this);
        parent::__destruct();
    }
    
    /**
     * Returns namespace that have intended data for this ActionNode as input data.
     * Otherwise null. What mean under Otherwise - this is when parent action nodes
     * have not in ArrayDataStorage have not any data that must be result of their
     * work. But passage to top is limited by the first parent action node which
     * have this action node as element of child branch.
     * 
     * @return string Returns namespace that have intended data for this ActionNode as input data. Otherwise null.
     */
    public function getIntendedDataNamespace(){
        $result = null;
        $ds= ArrayDataStorage::instance();
        
        $cNode = $this;
        while ( !is_null($cNode->parent) ) {
            $cNS = $ds->getOwnedNamespace($cNode->parent);
            $intendedData = $ds->getData($cNS);
            if ($intendedData !== null) {
                $result = $cNS;
                break;
            }
            if ( !is_null($cNode->parent->child) && $cNode->parent->child === $cNode ) break;
            $cNode = $cNode->parent;
        }
        return($result);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getNamespace(){
        return($this->getNodePath());
    }
    
    /**
     * 
     * 
     * 
     */
    public function run() {
        $ds = ArrayDataStorage::instance();
        $inDataNS = $this->getIntendedDataNamespace();
        
        $inData = $ds->getData($inDataNS);
        $callback = $this->getData();
        $outData = call_user_func($callback,$inData,$this);
        
        $ds->unsetData($this);
        if( !is_null($outData) ) {
            //$dataRef = $ds->setData($this)->getData($this);
            foreach((array) $outData as $key=>$value){
                $ds->setValue($key, $value, $this);// $dataRef->setData($key,$value,$dataRef->getOwner());
            }
        }
        
        if ( !is_null($this->child) ) $this->child->run(); 
        if ( !is_null($this->next) ) $this->next->run(); 
        
    }

}



//
//$callBack = function($data, ActionNode $actionNode){
//    $result=null;
//    $hash = spl_object_hash($actionNode);
//    $ns= $actionNode->getNamespace();
//    print($ns.':'.$hash."\r\n");
//    if ( is_null($data) ) $result = array($hash=>$ns);
//      else {
//          if ( $actionNode->getChild() !== null ) {
//              $result = array($hash=>$ns);
//          }
//            else $data->setValue($hash,$ns, $data->getOwner());
//      }
//    return($result);
//};
//
//$action= new ActionNode('MAIN ActionNode',$callBack);
//$action->setChild(new ActionNode('Child ActionNode of MAIN',$callBack))
//        ->setNext(new ActionNode('Next ActionNode for Child of MAIN',$callBack))
//        ->setChild(new ActionNode('Child ActionNode for MAIN_Child_Next of MAIN',$callBack))
//        ->setNext(new ActionNode('Next ActionNode for MAIN_Child_Next_Child of MAIN',$callBack))
//        ->setNext(new ActionNode('Next ActionNode of Next for Child of MAIN',$callBack));
//$action->setNext(new ActionNode('Next for MAIN',$callBack));
//
//$action->run();
//
//$ds = ArrayDataStorage::instance();
//print_r($ds);
//
//$action->printTree();