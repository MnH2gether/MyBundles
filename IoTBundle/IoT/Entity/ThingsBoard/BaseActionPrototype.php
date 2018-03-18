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

namespace IoT\Entity\ThingsBoard;


use IoT\Entity\Entity;
use IoT\Entity\Fields\Field;
use IoT\Entity\Transports\ITransport;

/**
 * Generated: Nov 22, 2017 12:36:29 AM
 * 
 * Description of BaseActionPrototype
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
abstract class BaseActionPrototype implements \Serializable {

    /**
     *
     * @var \IoT\Entity\Transports\ITransport
     */
    protected $transport = null;
    
    protected $onError = null;

    /**
     *
     * @var Url
     */
    private $url = null;
    
    public function __construct() {
        $this->url = new Url('');
        $this->setTransport(); //that can be invoked only in consructor and only first time.
    }

    /**
     * 
     * Must realize logic like if $trancport is null and  getTransport() is null too
     * then we must create new appropriate instance otherwise if $trancport !== null then 
     * set $this->transport in this value else ($trancport and getTransport() !== null)
     * nothing do. 
     * 
     * @return \IoT\Entity\ThingsBoard\BaseAction Itself;
     */
    abstract public function setTransport(ITransport $transport = null);
    
    
    /**
     * 
     * @return \IoT\Entity\Transports\ITransport;
     */
    public function getTransport(){
        return($this->transport);
    }    
    
    /**
     * 
     * @return \IoT\Entity\ThingsBoard\Url Returns Url holder.
     */
    public function getUrl(){
        return($this->url);
    }
    
    protected function _setFieldEntity(Field $field, Entity $entity){
        $entity->setField($field);
        return($this);
    }

    protected function _getFieldsEntity(Entity $entity){
        return($entity->getFields($entity));
    }

    protected function _getFieldEntity($name, Entity $entity){
        $result= null;
        foreach($this->_getFieldsEntity($entity) as $field){
            if ($field->getName() === $name){
                $result= $field;
                break;
            }
        }
        return($result);
    }

    protected function _removeFieldEntity(Field $field, Entity $entity){
        foreach($this->_getFieldsEntity($entity) as $index=>$fld){
            if ($field === $fld){
                $entity->removeField($index);
                break;
            }
        }
        return($this);
    }
    
    protected function _clearFieldEntity(Entity $entity){
        $entity->clearFields();
        return($this);
    }
    
    /**
     * 
     */
    abstract protected function assemblyUrl($pathTo, $version='');

    
    /**
     * In callback will transmit one parameter is \IoT\Entity\ThingsBoard\Device $device
     * 
     * @param Callable $callback
     * @return \IoT\Entity\ThingsBoard\Device Itself
     */
    public function setOnError(Callable $callback){
        $this->onError = $callback;
        return($this);
    }
    
    /**
     * For internal purposes and realization of DRY principle.
     * Can be helpful for using in public run() function
     * 
     * Attention !!!
     * $type can be 'fields' for this object (only lower case letters) or some word what must exists in two places 
     * must exists method getFields or getAttributes for other example.
     * also \IoT\Entity\ThingsBoard\APIInfo::REST must contain Url for same named keys 'fields' under Key with value -> $Entity
     * for example if $Entity = 'Device' you must have in \IoT\Entity\ThingsBoard\APIInfo::REST the ....'Device'=>array('fields'=>'some url to REST API')...
     *
     * @param string $Entity
     * @param string $type
     * @return false|string result of execution.
     */
    protected function _differentialRun($Entity, $type){
        $result= false;
        $prop = strtolower($type);
        $method = 'get'.ucfirst($prop);
        if( property_exists($this,$prop) && method_exists($this,$method) && count( $this->$method() ) > 0  ){
            $this->transport->setDataSource($this->$prop);
        } else {
            throw new \Exception('Property '.static::class.'::'.$prop.' or method '.static::class.'::'.$method.' (or same in parent classes) does not exists or empty array was returned by method.'); 
        }
        //!!!!!!!!!!!!!!!!! setUrl() not exists in ITransport 
        $this->transport->setUrl($this->assemblyUrl("$Entity".'.'.$prop));
        $result= $this->transport->run();
        if ( $result === false && is_callable($this->onError) ) { call_user_func($this->onError, $this); }
        return($result);
    }
    
    
    abstract public function run();

    
    public function serialize() {
        //Nothig need to serialze.
        //protected $transport property must be serialized in descendent where implemented setTransport(...) method.
        return null;
    }
    
    public function unserialize( $serialized ) {
        $this->url = new Url('');
    }
    
    
}

