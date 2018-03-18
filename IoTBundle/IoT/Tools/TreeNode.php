<?php
/*
 * TreeNode (reincarnation of treetecs.com project)
 * 
 * Copyright (c) 2012-2017, Semyon Mamonov <semyon.mamonov@gmail.com>.
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
 * 
 */

namespace IoT\Tools;


/**
 * TreeNode
 * 
 * <pre>
 * Description of TreeNode
 *                  main
 *           |----------------|         |----------------|
 *           |    TreeNode    |->child->|    TreeNode    |
 *           |----------------|<-parent-|----------------|
 *               |       ^
 *              next   parent
 *               |       |
 *               V       |
 *           |----------------|         |----------------|         |----------------|
 *           |    TreeNode    |->child->|    TreeNode    |->child->|    TreeNode    |
 *           |----------------|         |----------------|         |----------------|
 *           |    TreeNode    |             V  next V
 *           |----------------|         |----------------|
 *           |    TreeNode    |         |    TreeNode    |
 *           |----------------|         |----------------|
 * </pre>
 * 
 * Generated: Dec, 2017 12:15:25 AM
 * 
 * @copyright Semyon Mamonov <semyon.mamonov@gmail.com>
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class TreeNode {
    
    static public $counter = 0; //For internal infos.
    
    /**
     * @var TreeNode Reference on parent ( previous sibling that lay above this ) node   
     */
    protected $parent = null;//parent TreeNode
    /**
     * @var TreeNode Reference on first child ( right side ) node   
     */
    protected $child = null; //child TreeNode
    /**
     * @var TreeNode Reference on next ( sibling that lay lower this ) node   
     */
    protected $next = null;  //next (down sibling) TreeNode 
    
    /**
     * @var mixed Data that related with node 
     */
    protected $data = null; //any data
    
    /**
     * @var string Node's name  
     */
    protected $name = ''; //name of data
    
    /**
     * @var integer By default it integer but can by any type. General purpose is sort of Nodes  
     */
    protected $id = 0;//unique identificator. For internal infos.
    
    public function __construct($name = '', $data = null) {
        self::$counter++;
        $this->id = self::$counter;
        $this->data = $data;
        $this->name = $name;
    }
    
    public function __destruct() {
        //for testing purposes and have sense only in couple with gc_collect_cycles()
        self::$counter--;
    }
    
    protected function getterDefault($propName){ 
        $propName = strtolower($propName);
        if( !property_exists($this, $propName) ) throw new \Exception ('Property does not exists: \''.static::class.'::'.$propName.'\'');
        return($this->$propName);
    }
    
    /**
     * @return TreeNode Reference at parent TreeNode object
     */
    public function getParent(){
        return $this->getterDefault(preg_replace('/^get([[:alnum:]_]+)/','${1}', __FUNCTION__));
    }

    /**
     * @return TreeNode Reference at first child TreeNode object
     */
    public function getChild(){
        return $this->getterDefault(preg_replace('/^get([[:alnum:]_]+)/','${1}', __FUNCTION__));
    }
    
    /**
     * @return TreeNode Reference at next TreeNode object
     */
    public function getNext(){
        return $this->getterDefault(preg_replace('/^get([[:alnum:]_]+)/','${1}', __FUNCTION__));
    }
    
    /**
     * 
     * @return integer
     */
    public function getId(){
        return $this->getterDefault(preg_replace('/^get([[:alnum:]_]+)/','${1}', __FUNCTION__));
    }

    /**
     * 
     * @return mixed
     */
    public function getData(){
        return $this->getterDefault(preg_replace('/^get([[:alnum:]_]+)/','${1}', __FUNCTION__));
    }

    /**
     * 
     * @return string
     */
    public function getName(){
        return $this->getterDefault(preg_replace('/^get([[:alnum:]_]+)/','${1}', __FUNCTION__));
    }
    
    /**
     * This very specifically function for testing case when current node is child
     * for parent node which have child branch where is current node. It because
     * the detach functionality repair only next and parent relations but not
     * child's. Therefor when we will do setNext or ... we must test this case 
     * for avoid circle reference and memory leak at same time. 
     * 
     * @param \IoT\Tools\TreeNode $parentNode
     * @return boolean 
     */
    private function _amIChildFor(TreeNode $parentChildNode){
        $result = false;
        if ($parentChildNode !== null && $parentChildNode->child !== null ){ 
            $node = $this;
            while ( !is_null($node) ){
                if( !is_null($node->parent) && $node->parent->child === $node && $node->parent === $parentChildNode) {
                    $result = true;
                    break;
                }
                $node = $node->parent;
            }
        }
        
        return($result);
    }
    
    /**
     * 
     * @param TreeNode $node Node that will parent node for this object.
     */
    public function setParent(TreeNode $node = null){
        if( is_null($node) ) throw  new Exception ('You can\'t set reference at parent object in null, instead use detachParent() method of this object.');
        if( $this->_amIChildFor($node) ) throw new Exception ('Current node is child for parent node which have child branch where is current node. As result of this action will be circular reference and memory leak at one time.');
        
        //detach new parent $node if it still within other chain
        $node->detach();
        if ( !is_null($this->parent) ) {
            if( $this->parent->next === $this) $this->parent->next = $node;
             else if ($this->parent->child === $this) $this->parent->child = $node;
              else  throw new Exception('Parent exists but this object no belongs to either next or child branches.');
            $node->parent = $this->parent;
            $this->parent = null;
        }
        
        $this->parent = $node;
        $node->next = $this;

        return($node);
    }
    
    
    /**
     * @param TreeNode $next Node that will be related as next (down sibling) node for current (this) node.
     * @return TreeNode Node that was set up as next for current.
     */
    public function setNext(TreeNode $node = null){
        if( is_null($node) ) throw  new Exception ('You can\'t set reference at next object in null, instead use detachNext() method of next object.');
        if( $this->_amIChildFor($node) ) throw new Exception ('Current node is child for parent node which have child branch where is current node. As result of this action will be circular reference and memory leak at one time.');

        //detach new parent $node if it still within other chain
        $node->detach();
        if ( !is_null($this->next) ) {
            $this->next->parent = $node;
            $node->next = $this->next;
            $this->next = null;
        }
        
        $this->next = $node;
        $node->parent = $this;

        return($node);
    }

    
    /**
     * 
     * @param TreeNode $node Node that will first child node for this object.
     */
    public function setChild(TreeNode $node = null){
        if( is_null($node) ) throw  new Exception ('You can\'t set reference at first child object in null, instead use detachChild() method of this object.');
        if( $this->_amIChildFor($node) ) throw new Exception ('Current node is child for parent node which have child branch where is current node. As result of this action will be circular reference and memory leak at one time.');
        
        //detach new parent $node if it still within other chain
        $node->detach();
        if ( !is_null($this->child) ) {
            $this->child->parent = $node;
            $node->next = $this->child;
            $this->child = null;
        }
        
        $this->child = $node;
        $node->parent = $this;

        return($node);
    }
    
    
    public function setId($Id){
        $this->id = $Id;
        return($this);
    }
    
    
    public function setData($data){
        $this->data = $data;
        return($this);
    }

    
    public function setName($name){
        $this->name = $name;
        return($this);
    }
    
    
    /**
     * Take pay attention You responsibility for to store returned TreeNode object
     * or keep reference on this TreeNode object reachable and You must manually invoke
     * free() method if need for destroy children chain and avoid memory leak.
     * If returned TreeNode object will lost then children chain will not be free
     * in memory because within have circular references.
     * 
     * @return TreeNode Returns itself but prepared for either insert into other palace or free. 
     */
    public function detach(){
        if ( !is_null($this->parent) ) {
            if( $this->parent->next === $this) $this->parent->next = $this->next;
             else if ($this->parent->child === $this) $this->parent->child = $this->next;
              else  throw new Exception('Parent exists but this object no belongs to either next or child branches.');
        }
        if ( !is_null($this->next) ) $this->next->parent = $this->parent;
        $this->parent = null;
        $this->next = null;

        return($this);
    }

    /**
     * Take pay attention You responsibility for to store returned TreeNode object
     * or keep reference on this or its super TreeNode object in reachable border.
     * If you want get node that is topmost parent node then to invoke method
     * getFirstParent() of returned TreeNode object.
     * 
     * @return TreeNode Returns parent TreeNode object 
     */
    public function detachParent(){
        $parent = $this->parent;
        if ( !is_null($this->parent) ) {
            if( $this->parent->next === $this) $this->parent->next = null;
             else if ($this->parent->child === $this) $this->parent->child = null;
              else  throw new \Exception('Parent exists but this object no belongs to either next or child branches.');
        }
        $this->parent = null;

        return($parent);
    }

    
    /**
     * Almost alias of TreeNode::detachParent() because
     * it makes detachParent() for child property if it not null.
     * 
     * @see detachParent() 
     * @return TreeNode Returns parent TreeNode object 
     */
    public function detachChild(){
        $child = $this->child;
        if ( !is_null($child) ) {
            $child->detachParent();
        }
        return($child);
    }
    
    
    /**
     * Take pay attention You responsibility for to store returned TreeNode object
     * or keep reference on this TreeNode object in reachable border.
     * With returned TreeNode you get the topmost TreeNoode that is top of new chain.
     * 
     * @return TreeNode Returns parent TreeNode object 
     */
    public function detachNext(){
        $next = $this->next;
        if ( !is_null($this->next) ) $this->next->parent = null;
        $this->next = null;

        return($next);
    }
    
    
    /**
     * 
     */
    public function free($doDetachFirst = true){
        if( $doDetachFirst ) $this->detach();
        
        if (!is_null($this->child)) $this->child->free(false);
        if (!is_null($this->next)) $this->next->free(false);

        $this->setData(null)->setName(null);
        $this->detachParent();
    }
    
    
    /**
     * Will run callback start from deepest child node slow go up to top.
     * So named bubble ( bubbling ) principle. In this case the value that can be returned 
     * from callback nothing means because recursion always will done up to end.
     *  
     * @param \IoT\Tools\callable $callback
     */
    public function bubbleWalk(callable $callback){
        if (!is_null($this->child)) $this->child->bubbleWalk($callback);
        if (!is_null($this->next)) $this->next->bubbleWalk($callback);
        
        call_user_func($callback, $this);
    }
    
    
    /**
     * 
     * Will run callback start from current node and will traverse to down.
     * So named capture (capturing) principle. In this case the value that can be returned 
     * from callback have sense because you can break further execution. 
     * If Callback will returns False ( exact === ) then further traverse
     * to down will be break.
     * 
     * @param \IoT\Tools\callable $callback
     */
    public function captureWalk(callable $callback){
        $node = $this;
        
        while (!is_null($node)){
            $res = call_user_func($callback,$node);
            if( $res === false ) break;
            if ( !is_null($node->child) ) $node->child->captureWalk ($callback); 
            $node = $node->next;
        }
    }
    

    /**
     * 
     * @return array 
     */
    public function dataListDump(){
        $result = array();
        $funcGetData= function ($tObj) use (&$result) {
            $id= $tObj->id;
            $result["$id"]['data']=$tObj->data;
            $result["$id"]['name']=$tObj->name;
            $result["$id"]['id']=$id;
            if ($tObj->parent !== null){
                $result["$id"]['parent_id']=$tObj->parent->id;
                if ($tObj->parent->child === $tObj) $result["$id"]['relation']='is CHILD';
                 else if ($tObj->parent->next === $tObj) $result["$id"]['relation']='is NEXT';
                  else $result["$id"]['relation']='is BROKEN';
            } else $result["$id"]['parent_id']='null';
            return(true);
        };
        
        $this->bubbleWalk($funcGetData);
        return($result);
    }

    
    /**
     * Print TreeNode structure start from current and to child and down.
     * @return null 
     */
    public function printTree($indent=''){
        $node = $this;
        while (!is_null($node)){
            print($indent.$node->getName().':'.spl_object_hash($node)."\r\n"); 
            if ( !is_null($node->child) ) {
                $node->child->printTree( str_repeat( $indent === '' ? '  ' : $indent , 2) ); 
            }
            $node = $node->next;
        }
    }
    

}

//
//print(xdebug_memory_usage()."\r\n");
//$test = new TreeNode('1Main', 1);
//
//print(xdebug_memory_usage()."\r\n");
//$funcName = array( 'setNext', 'setChild', 'setParent');
//$funcNameCnt = count($funcName);
//
//print('Memory usage at start \''.xdebug_memory_usage()."'\r\n");
//$step = 1;
//print('Static counter at start \''.TreeNode::$counter."'\r\n");
//while ($step < 1000){
//    print("===========\r\n");
//    print("Memory usage at step #$step '".xdebug_memory_usage()."'\r\n");
//    $count = 1;
//    $tobj= $test;
//    while ($count <= 500 ){
//      $fncIdx= mt_rand(0, $funcNameCnt-1);
//
//      $tobj= call_user_func(array($tobj,$funcName[$fncIdx]), new TreeNOde("{$count}".$funcName[$fncIdx], $count) );
//      $count++;
//    }
//    print('Memory usage after load 500 elements \''.xdebug_memory_usage()."'\r\n");
//    print('Static counter after load \''.TreeNode::$counter."'\r\n");
//    $test->free();
//    print('Static counter after free \''.TreeNode::$counter."'\r\n");
//    print('Memory usage after free 500 elements \''.xdebug_memory_usage()."'\r\n");
//    gc_collect_cycles();
//    print('Memory usage after gc_collect_cycles \''.xdebug_memory_usage()."'\r\n");
//    print('Static counter after gc_collect_cycles \''.TreeNode::$counter."'\r\n");
//    $step++;
//    sleep(0);
//}
//print("===========\r\n");
//print('Memory usage at finish \''.xdebug_memory_usage()."'\r\n");
//
//$tobj->free();
//unset($tobj);
//gc_collect_cycles();
//print('Static counter after free $tobj \''.TreeNode::$counter."'\r\n");
//print('Memory usage after gc_collect_cycles \''.xdebug_memory_usage()."'\r\n");
//var_dump( $test->dataListDump() );
//print("======== Memory usage test passed. But result you must do youself.\r\n");
//
//
//
//$test->setChild(new TreeNode("2Child",2) )->setNext(new TreeNode("3Next",3))->setNext(new TreeNode("4Next",4))->setChild(new TreeNode("5Child",5));
//var_dump( $test->dataListDump() );
//print("======== Creation relatives test passed. But result you must do youself.\r\n");
//
//
//print('Memory usage at start \''.xdebug_memory_usage()."'\r\n");
//print('Static counter at start \''.TreeNode::$counter."'\r\n");
//$count = 1;
//$tobj= $test;
//while ($count <= 1500 ){
//  $fncIdx= mt_rand(0, $funcNameCnt-1);
//  $tobj= call_user_func(array($tobj,$funcName[$fncIdx]), new TreeNOde("{$count}".$funcName[$fncIdx], $count) );
//  $count++;
//}
//print('Memory usage after load 1500 elements \''.xdebug_memory_usage()."'\r\n");
//gc_collect_cycles();
//print('Memory usage after gc_collect_cycles \''.xdebug_memory_usage()."'\r\n");
//print('Static counter after gc_collect_cycles \''.TreeNode::$counter."'\r\n");
//
//unset($tobj);
//gc_collect_cycles();
//print('Static counter after free $tobj -> (interim TreeNode) \''.TreeNode::$counter."'\r\n");
//print('Memory usage after gc_collect_cycles \''.xdebug_memory_usage()."'\r\n");
//
//unset($test);
//gc_collect_cycles();
//print('Static counter after free unset($test) -> (main TreeNode) \''.TreeNode::$counter."'\r\n");
//print('Memory usage after gc_collect_cycles \''.xdebug_memory_usage()."'\r\n");

