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

namespace IoT\Entity\ThingsBoard\REST;

use IoT\Entity\Entity;

/**
 * Generated: Nov 19, 2017 7:16:08 PM
 * 
 * Description of TimeSeries
 * 
 * @link https://thingsboard.io/docs/user-guide/telemetry/#telemetry-plugin Source for v1.3 
 * 
 * Methods must be GET type for all examples.
 * 
 * *****************************************************************************
 * http(s)://host:port/api/plugins/telemetry/{entityType}/{entityId}/keys/timeseries
 * 
 * <b>{entityType}</b> - Supported entity types are: TENANT, CUSTOMER, USER, RULE, PLUGIN, DASHBOARD, ASSET, DEVICE, ALARM
 * <b>{entityId}</b> - can be got from adminutility.
 * 
 * curl -v -X GET http://localhost:8080/api/plugins/telemetry/DEVICE/ac8e6020-ae99-11e6-b9bd-2b15845ada4e/keys/timeseries \
 * --header "Content-Type:application/json" \
 * --header "X-Authorization: $JWT_TOKEN"
 * 
 * Result: ["gas","temperature"]
 * 
 * *****************************************************************************
 * http(s)://host:port/api/plugins/telemetry/{entityType}/{entityId}/values/timeseries?keys=key1,key2,key3
 * 
 * curl -v -X GET http://localhost:8080/api/plugins/telemetry/DEVICE/ac8e6020-ae99-11e6-b9bd-2b15845ada4e/values/timeseries?keys=gas,temperature \
 * --header "Content-Type:application/json" \
 * --header "X-Authorization: $JWT_TOKEN"
 * 
 * 
 * Result:
 * 
 * { "gas": [ {"ts": 1479735870786,"value": "1"} ],
 *   "temperature": [ {"ts": 1479735870786,"value": "3"} ] }
 * 
 * Or
 * 
 * http(s)://host:port/api/plugins/telemetry/{entityType}/{entityId}/values/timeseries?keys=key1,key2,key3&startTs=1479735870785&endTs=1479735871858&interval=60000&limit=100&agg=AVG
 * 
 * The supported parameters are described below:
 * 
 * <b>keys</b> - comma separated list of telemetry keys to fetch.
 * <b>startTs</b> - unix timestamp that identifies start of the interval in milliseconds.
 * <b>endTs</b> - unix timestamp that identifies end of the interval in milliseconds.
 * <b>interval</b> - the aggregation interval, in milliseconds.
 * <b>agg</b> - the aggregation function. One of MIN, MAX, AVG, SUM, COUNT, NONE.
 * <b>limit</b> - the max amount of data points to return or intervals to process.
 * 
 * curl -v -X GET "http://localhost:8080/api/plugins/telemetry/DEVICE/ac8e6020-ae99-11e6-b9bd-2b15845ada4e/values/timeseries?keys=gas,temperature&startTs=1479735870785&endTs=1479735871858&interval=60000&limit=100&agg=AVG" \
 * --header "Content-Type:application/json" \
 * --header "X-Authorization: $JWT_TOKEN"
 * 
 * Result: 
 * 
 * { "gas": [ {"ts": 1479735870786, "value": "1"},{"ts": 1479735871857, "value": "2"}],
 *   "temperature": [ {"ts": 1479735870786, "value": "3"}, {"ts": 1479735871857, "value": "4"} ] }
 * 
 * @property integer $startTs - unix timestamp that identifies start of the interval in milliseconds.
 * @property integer $endTs - unix timestamp that identifies end of the interval in milliseconds.
 * @property integer $interval - the aggregation interval, in milliseconds.
 * @property string $agg - the aggregation function. One of MIN, MAX, AVG, SUM, COUNT, NONE.
 * @property integer $limit - the max amount of data points to return or intervals to process.
 * @property \ArrayObject $keys Returns list of keys that will fetch from server.
 * @property-read \ArrayObject $keys[$i] Returns key with index $i that will fetch from server.
 * @property-write \ArrayObject $keys[$i]=$key Rewrite/write to list keys with index $i and $key that after will fetched from server. If index not exists then will add.
 * 
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class TimeSeries extends BaseActionPrototype {
    //put your code here
    
    /**
     * @var \ArrayObject 
     */
    protected $keys = null;
    
    /**
     *
     * @var integer unix timestamp that identifies start of the interval in milliseconds.
     */
    protected $startTs = null;  //unix timestamp that identifies start of the interval in milliseconds.
 
    /**
     *
     * @var integer unix timestamp that identifies end of the interval in milliseconds.
     */
    protected $endTs = null;    //unix timestamp that identifies end of the interval in milliseconds.
    
    /**
     *
     * @var integer the aggregation interval, in milliseconds.
     */
    protected $interval = null; //the aggregation interval, in milliseconds.
    
    /**
     *
     * @var string  the aggregation function. One of MIN, MAX, AVG, SUM, COUNT, NONE.
     */
    protected $agg = null;      //the aggregation function. One of MIN, MAX, AVG, SUM, COUNT, NONE.
    
    /**
     *
     * @var integer  the max amount of data points to return or intervals to process.
     */
    protected $limit = null;    // the max amount of data points to return or intervals to process.
    

    /**
     * @var \ArrayObject 
     */
    private $values = null;
    
    /**
     *
     * @var string Can be one of TENANT, CUSTOMER, USER, RULE, PLUGIN, DASHBOARD, ASSET, DEVICE, ALARM
     */
    protected $entityType =''; //TENANT, CUSTOMER, USER, RULE, PLUGIN, DASHBOARD, ASSET, DEVICE, ALARM
    
    /**
     *
     * @var string  Valid EntityID from ThingsBoard. something like 'f41e7860-bcf0-11e7-97fe-8fcf74e946dc'
     */
    protected $entityId =''; //valid EntityID from ThingsBoard
    
    protected $userAccessToken =''; //

    //'DEVICE'->'f41e7860-bcf0-11e7-97fe-8fcf74e946dc'
    
    public function __construct($entityType, $entityId, $userAccessToken = '') {
        parent::__construct();
        $this->values = new \ArrayObject();
        $this->keys = new \ArrayObject();
        $this->setEntityType($entityType)->setEntityId($entityId)->setUserAccessToken($userAccessToken);
    }
    
    /**
     * 
     * @param string $entityType
     * @return \IoT\Entity\ThingsBoard\REST\TimeSeries Itself
     */
    public function setEntityType($entityType){
       $types = array('TENANT', 'CUSTOMER', 'USER', 'RULE', 'PLUGIN', 'DASHBOARD', 'ASSET', 'DEVICE', 'ALARM');
       $entityType = strtoupper(trim($entityType));
       if(!empty($entityType) && in_array($entityType, $types)){
           $this->entityType = $entityType;
       } else throw new \Exception('Entity type \''.$entityType.'\' not supported.');  
       
       return($this);
    }

    /**
     * 
     * @return string Entity Type
     */
    public function getEntityType(){
        return($this->entityType);
    }    
    
    /**
     * 
     * @param string $entityId
     * @return \IoT\Entity\ThingsBoard\REST\TimeSeries Itself
     */
    public function setEntityId($entityId){
       $this->entityId = trim($entityId);
       return($this);
    }
    
    /**
     * 
     * @return string EntityType
     */
    public function getEntityId(){
       return($this->entityId);
    }

    /**
     * 
     * @param string $userAccessToken
     * @return \IoT\Entity\ThingsBoard\REST\TimeSeries Itself
     */
    public function setUserAccessToken($userAccessToken){
       $this->userAccessToken = trim($userAccessToken);
       return($this);
    }
    
    /**
     * 
     * @return string UserAccessToken
     */
    public function getUserAccessToken(){
       return($this->userAccessToken);
    }
    
    protected function getTokenValues(array $tokenNames) {
        $result = array();
        //Supported tokens -> {entityType}, {entityId}
        foreach($tokenNames as $tokenName){
            $propName = substr($tokenName,1,-1);
            if ( isset($this->$propName )) $result["$tokenName"]=$this->$propName;
              else  throw new \Exception('Token with name \''.$propName.'\' not supported by this object \IoT\Entity\ThingsBoard\REST\TimeSeries.');  
        }
        return($result);
    }
    
    protected function getQueryData(){
        $result = array();
        if ( count($this->keys) > 0) $result['keys']= implode(',',(array)$this->keys);
        $qDataSetings = array(
            'startTs'=>'strval',
            'endTs'=>'strval',
            'interval'=>'intval',
            'agg'=>'strval',
            'limit'=>'intval'
        );
        foreach ( $qDataSetings as $param=>$callback){
            if($this->$param !== null) $result["$param"]= call_user_func($callback, $this->$param);
        }
        return($result);
    }

    public function run() {
        $result = false;

        $transport= $this->getTransport();
        $transport->setMethod('GET');
        
        $transport->setOnConfig(function(\IoT\Entity\Transports\HTTPServers\ThingsBoard $transport) {
            $ch= $transport->getHandler();
            //https://thingsboard.io/docs/reference/rest-api/
            //on the bottom of page >>> Now, you should set ‘X-Authorization’ to “Bearer $YOUR_JWT_TOKEN”
            curl_setopt($ch,CURLOPT_HTTPHEADER,array( "Content-Type: application/json", "X-Authorization: ".$this->getUserAccessToken() ) );
        });
         
        if ( $this->keys->count() === 0 ){
            $resultOfFirstRun = $this->_differentialRun('TimeSeries','keys');
            if ( $resultOfFirstRun === false ) throw new Exception ('Was wrong authorization stage.');
            $this->keys->exchangeArray( json_decode($resultOfFirstRun,true) );
        }
            
        $this->getUrl()->setQuery( $this->getQueryData() );
        $resultOfSecondRun = $this->_differentialRun('TimeSeries','values');
        
        if ( $resultOfSecondRun !== false ) $this->values->exchangeArray( json_decode($resultOfSecondRun,true) );

        return($this->values);
    }
    
    
    protected function getValues(){
        return($this->values);
    }
    
    protected function getKeys(){
        return($this->keys);
    }

    protected function setKeys($keys){
        $arrayKeys = (array) $keys;
        $this->getKeys()->exchangeArray($arrayKeys);
        return($this->keys);
    }

    
    public function __get($name) {
        $result = null;
        $getter = 'get'. ucfirst($name);
        if( method_exists($this, $getter) ){
            $method = new \ReflectionMethod($this,$getter);
            if( count( $method->getParameters() ) == 0 ) return( call_user_func_array(array($this, $getter)) );
        }
        if( property_exists($this, $name) ) {
            $prop = new \ReflectionProperty($this,$name);
            if ( !$prop->isPrivate() ) $result= $this->$name;
        }
        return($result);
    }
    
    public function __set($name, $value) {
        $setter = 'set'. ucfirst($name);
        if( method_exists($this, $setter) ){
            $method = new \ReflectionMethod($this,$setter);
            if( count( $method->getParameters() ) == 1 ) {
                call_user_func(array($this,$setter), $value);
                return;
            }
        }
        if( property_exists($this, $name) ) {
            $prop = new \ReflectionProperty($this,$name);
            if ( !$prop->isPrivate() ) $this->$name= $value;
        }
    }
    

}
