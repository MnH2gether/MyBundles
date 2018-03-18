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

//You will need to uncomment for testing purposes.
//include './TreeNode.php';

/**
 * Generated: Dec 14, 2017 12:15:25 AM
 * 
 * Description of TreeNodeEx
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class TreeNodeEx extends TreeNode{
    //put your code here
    
    /**
     * For internal usage only.
     *  
     * @param string $ncName 'next' or 'child' - names of properties where will search last element 
     * @return TreeNode
     */
    private function _getLast($ncName){
        $obj= $this;
        while (!is_null($obj->$ncName)) $obj = $obj->$ncName;
        return($obj);
    }
    

    /**
     * Get last node in 'next' chain for this TreeNode.
     * @return TreeNode 
     */
    public function getLast(){
        return($this->_getLast('next'));
    }


    /**
     * Get last node in 'child' chain for this TreeNode.
     * 
     * @return TreeNode 
     */
    public function getLastChild(){
        $node = $this;
        if ( !is_null($node->child) ) $node = $node->child->getLast();
         else throw new Exception ('TreeNode with name \''.$this->getName().'\' has not children ellements.');
        return($node);
    }

    /**
     * Returns first element of current chain.
     * It will be or topmost TreeNode element or first element in child branch. 
     * 
     * @return TreeNode
     */
    public function getFirst(){
        $node = $this;
        while ( !is_null($node->parent) ){
            if ( !is_null($node->parent->child) &&  $node->parent->child === $node) break;
            $node = $node->parent;
        }
        return($node);
    } 

    /**
     * Returns root TreeNode for tree which this TreeNode belongs to.
     * 
     * @return TreeNode
     */
    public function getMain(){
        $node = $this;
        while ( !is_null($node->parent) ){
            $node = $node->parent;
        }
        return($node);
    }
    
    
    /**
     * Searching the Tree Nodes that satisfied by $callback function.
     * The traversing  of elements make from top to bottom.
     * Between 'next' and 'child' branches will be selected 'child' branch.. 
     * 
     * @param \callable $callback Callback that will invoked for comparison purposes.
     * Into callback will be passed TreeNode element. Callback must return exact True value if this
     * TreeNode must be included in result. 
     * @param integer $count 0 means without stop - need find all matched elements.
     * Negative or positive value mean that need stop TreeNode traversing after
     * will reached the amount matched elements equal to count.  
     * @param \IoT\Tools\TreeNode $startNode If need start traversing from node different to current you can specify it here.
     * @return array Array of TreeNode-s that were found.
     */
    public function find(callable $callback, $count = 0, TreeNode $startNode = null){
        $result = array();
        $node = $this;
        if ( $startNode !== null ) {
            $node = $startNode;
        }
        $cCount =  ( $count === 0 ? -1 : abs($count) ) ;
        $node->captureWalk(function (TreeNode $node) use (&$result, &$cCount, $callback) {
            $res = true;
            if ( $cCount !== 0 ) {
                if ( call_user_func($callback,$node) === true ) {
                    $result[]=$node;
                    $cCount--;
                }
            } else {
                $res = false;
            }
            return($res);
        });
        
        return($result);
    }

    /**
     * Returns separator of nesting. Can be redefined in descendants.
     * @return string Separator
     */
    public function getSeparatorOfNesting(){
        return('.');
    }

    /**
     * Returns name as array elements that splitted by separator of nesting on the less elements. Can be redefined in descendants.
     * 
     * @param string $fullName Something like XPath.
     * @return array 
     */
    protected function getSplittedName($fullName){
        return( explode($this->getSeparatorOfNesting(), $fullName) );
    }
    
    /**
     * 
     * @param string $needle
     * @param string $haystack
     * @return boolean True if $needle is subset of $hashStack.
     */
    protected function subSetComparator($needle, $haystack){
        $arrNeedles = $this->getSplittedName($needle);
        $cntNeedles = count($arrNeedles);
        $result = array_fill(0, $cntNeedles, 0);
        
        $arrHaystacks = $this->getSplittedName($haystack);
        
        for( $i = 0; $i < $cntNeedles; $i++ ) {
            $index = array_search($arrNeedles[$i], $arrHaystacks);
            if ( $index !== false ) {
                $result[$i] = 1;
                $arrHaystacks = array_slice($arrHaystacks, $index+1);
            } else {
                break;
            }
        }
        
        return(array_sum($result) === $cntNeedles);
    }
    
    /**
     * Will returns node's path something like XPath. If in node name will found
     * IoT\Tools\TreeNodeEx::getSeparatorOfNesting() sequence of chars then it will be 
     * replaced by '_XXXX_' string.
     * 
     * @param \IoT\Tools\TreeNode $node This start node or current node otherwise.
     * @return string
     */
    public function getNodePath(TreeNode $node = null ){
        $result= array();
        $cNode = $this;
        if ( !is_null($node) ) $cNode = $node;
        
        $getSafeNodeName = function(TreeNode $node) {
            $name = $node->getName();
            $result = str_replace($this->getSeparatorOfNesting(),'_XXXX_', $name );
            if ($name !== $result ) {
                trigger_error ('Name of node \''.$name.'\' contains special character(s) \''.$this->getSeparatorOfNesting()
                        .'\' defined by TreeNodeEx::getSeparatorOfNesting() function.', E_USER_WARNING);
            }
            return( $result );
        };
        
        $result[]= $getSafeNodeName($cNode);
        while ( !is_null($cNode->parent) ) {
            if ( !is_null($cNode->parent->child) && $cNode->parent->child === $cNode ){
                $result[]= $getSafeNodeName($cNode->parent);
            }
            $cNode = $cNode->parent;
        }
        
        return( implode($this->getSeparatorOfNesting(), array_reverse($result)) ); 
    }
    
    /**
     * It compare $name with TreeNodeEx node name and if they are equal returns
     * reference on this node. The search doing start from current node and go 
     * top of tree until reached first node with same name. Difference consists
     * in names which comparing. in this function using exact comparision
     * of short names that returns $node->getName() method.
     * 
     * @param string $name
     * @return TreeNodeEx
     */
    public function getClosestByName($name){
        $result= null;
        $node = $this;
        do {
            if ($name === $node->getName()){
                $result = $node;
                break;
            }
            $node = $node->parent;
        } while( !is_null($node)  );

        return($result);
    }
    
    
    /**
     * 
     * @param string $name Full node name. For example for 'RootNodeName.ParentNodeName.MoreParentNode.MatchedNode' string
     * Will be to find MatchedNode that nested (not directly) in MoreParentNode node and etc up to RootNodeName.
     * @param \IoT\Tools\TreeNode $node This start node or current node otherwise.
     * @return array Array as key and value pair where key is node path and value is TreeNode.
     */
    public function getNodesByName($name, TreeNode $node = null){
        $result=array();
        $name = trim("$name");
        if ( !empty($name) ){
            $cNode = $this;
            if ( !is_null($node) ) $cNode = $node;

            $splittedName = $this->getSplittedName($name);
            $lastName = $splittedName[count($splittedName)-1];

            $tResult = $this->find(function(TreeNodeEx $node) use ($lastName){
                return( $node->getName() === $lastName ); 
            },0,$cNode);
            
            foreach($tResult as $node){
                $nodePath = $this->getNodePath($node);
                if( $this->subSetComparator($name, $nodePath) === true ) {
                    $result[$nodePath] = $node;
                }
            }
        }
        return($result);
    }
    
    
}

//
//$main = new TreeNodeEx('Main');
//
//$main->setNext(new TreeNodeEx('MainNext'))->setNext(new TreeNodeEx('MainNext'));
//$secondNextOfChild = $main->getNext()->setChild(new TreeNodeEx('MainNextChild') )->setNext(new TreeNodeEx('MainNextChildNext'));
//$secondNextOfChild->setChild(new TreeNodeEx('MainNextChildNextChild'))->setNext(new TreeNodeEx('MainNextChildNextChildNext'));
//$secondNextOfChild->setNext((new TreeNodeEx('MainNextChildNextNext')));
//
////main
////next->child
////      next ->child
////             next
////      next       
////next
//
//
//var_dump($main->dataListDump());
//
//$nodePath = $secondNextOfChild->getNodePath();
//print('\'id\' is -> '.$secondNextOfChild->getId()." for -> ");
//print($nodePath);
//$nodes = $main->getNodesByName($nodePath);
//if($nodes['MainNextChild.MainNextChildNext'] === $secondNextOfChild) {
//    print("\r\n".'getNodePath and getNodesByName work properly and reversible.');
//} else {
//    print("\r\n".'test of getNodePath and getNodesByName was fail.');
//}
