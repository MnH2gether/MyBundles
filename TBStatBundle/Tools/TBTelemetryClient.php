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

namespace TBStatBundle\Tools;

use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManager;

/**
 * Generated: Dec 1, 2017 6:28:02 PM
 * 
 * Description of TBTelemetryClient
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
abstract class TBTelemetryClient {
    //put your code here
    
    /**
     * 
     * @var \Psr\Container\ContainerInterface 
     */
    protected $container = null;
    
    /**
     *
     * @var \Doctrine\ORM\EntityManager 
     */
    protected $entityManager = null; 
    
    
    /**
     *
     * @var array of TBTElemetryEntityCondition 
     */
    protected $entities = array();
    
    /**
     * 
     * @param \Psr\Container\ContainerInterface $container Indeed $container will instance of
     *  Symfony\Component\DependencyInjection\Container but methods has() and get() are enough
     */
    public function __construct(ContainerInterface $container = null, $entityManagerName = '') {
        $this->container = $container;
        if("$entityManagerName" !== '' ) {
            $this->setEntityManager ($this->container->get('doctrine')->getManager("$entityManagerName"));
        }
    }
    
    /**
     * 
     * @param EntityManager $entityManager
     * @return TBTelemetryClient Itself
     */
    protected function setEntityManager(EntityManager $entityManager){
        $this->entityManager = $entityManager;
        return($this);
    }
    
    /**
     * Add entity condition in array of all conditions. Each conditions have AND logic between each element of within.
     * For example - will be find all information where $id == 'fff' && $key == 'fff' && $ts == 'fff' && $type == 'fff'
     * but if value is array then  $id ==  ( 'fff' || 'ff1' || 'ffn') etc.
     * But between each other they will have OR logic (entity condition 1) || (entity condition 2) || .....
     * 
     * @param mixed $id Can be either plain value or array. Device id(s) from ThingsBoard dashboard.   
     * @param mixed $key Can be either plain value or array. Metered parameter(s) for Device id(s) from ThingsBoard dashboard.
     * @param mixed $ts Can be either plain value or array. Day(s) from start of epoch like in Unix timestamp.
     *  GET ATTENSION This amount days without hour, min, sec as integer value. For example - intval(time()/60/60/24) 
     * @param mixed $type Can be either plain value or array. Type(s) of entities that available in ThingsBoard (see documentation). By default is DEVICE
     * @return TBTelemetryEntityCondition New created entity condition element.
     */
    public function addEntity($id, $key, $ts, $type='DEVICE'){
        $entityCondition = new TBTelemetryEntityCondition();
        $entityCondition->setEntityId($id)->setKey($key)->setTs($ts)->setEntityType($type);
        $this->entities[] = $entityCondition;
        return($entityCondition);
    }
    
    
    /**
     * 
     * @return array Array (list) of all TBTelemetryEntityCondition elements
     */
    public function getEntities(){
        return($this->entities);
    }
    
    /**
     * Clear list of all TBTelemetryEntityCondition elements
     * 
     * @return TBTelemetryClient Itself
     */
    public function clearEntities(){
        $this->entities= array();
        return($this);
    }
    
    /**
     * Remove one TBTelemetryEntityCondition element from list.
     * 
     * @param integer $index
     * @return \TBStatBundle\Tools\TBTelemetryEntityCondition Removed entity element
     */
    public function removeEntity($index){
        $result = null;
        $eCnt = count($this->entities);
        if ( $eCnt > 0 && $index > -1 &&  $index < $eCnt ) {
            list($result)= array_splice($this->entities,$index,1);
        }
        return($result);
    }
    
    
    protected function assemblySQL($entiiesGlue = "\r\nAND ", $innerGlue = "\r\nAND "){
        $tRes = array();
        $entsCnt = count($this->entities);
        $params = array();
        foreach ( $this->entities as $index=>$entity ) {
            $paramSufix = "";
            if ($entsCnt > 1) $paramSufix = "_$index";
            $rawsql = $entity->prepareSQL($paramSufix);
            $tRes[]= implode("$innerGlue", array_keys($rawsql));
            array_walk( $rawsql, function($value) use (&$params) {
                $params = array_merge($params,$value); 
            } );
        }
        
        $resultSQL = implode("$entiiesGlue", $tRes);
        return(array($resultSQL, $params));
    }
    
    /**
     * Must return parameterized SQL statement like described in PDO documentation.
     * 
     * @param string $where SQL statement (where clauses) that was assembled to based on EntityCondition elements.
     * @param array $params array of values for parameters in $where clauses.
     * @return string Description
     */
    abstract protected function getSQLStatement($where, array $params);
    
    
    protected function getFullSQL(){
        list($where, $params) = $this->assemblySQL(" )\r\nOR ( ");
        if( $where !== '' ) $where = "WHERE\r\n".'( '.$where.' )';
        
        $sql = $this->getSQLStatement($where, $params);
       return(array($sql,$params));
    }
    
    public function run() {
        if ( $this->entityManager == null ) throw new Exception ('Instance of \Doctrine\ORM\EntityManager to ThingsBoard database don\'t accessible.');
        $conn = $this->entityManager->getConnection();
        list ( $whereSQL, $params ) = $this->getFullSQL();
        //$queryBuilder->createNamedParameter($userInputEmail);
        //$query= $conn->createQueryBuilder();
        return($conn->fetchAll($whereSQL,$params));
    }
    
    
}
