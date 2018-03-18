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
use IoT\Entity\Fields\Field;

/**
 * Generated: Nov 12, 2017 11:25:28 PM
 * 
 * Description of Device
 *       
 *       $softDevice = new Device('hK46TABNw3fyvBRDj8qm');
 *
 *        $softDevice->setField( new RandomField('integerVal','integer') );
 *        $softDevice->setField( new RandomField('doubleVal','double') );
 *        $softDevice->setField( new RandomField('stringVal','string') );
 *        $softDevice->setField( new RandomField('booleanVal','boolean') );       
 *
 *        $softDevice->setAttribute( new Field('SerialNumber','integer',874173641) );
 *        $softDevice->setAttribute( new Field('Longitude','double',67.23) );
 *        $softDevice->setAttribute( new Field('Latitude','double',17.23) );
 *        $softDevice->setAttribute( new Field('GEOCoordinates','string','latitude: 66666, longitude: 666666') );
 *        $softDevice->setAttribute( new Field('IsActive','boolean',true) );       
 *
 *        $softDevice->getUrl()->setPort('8080');
 *        $softDevice->setOnError(
 *            function (Device $device) use ($monoLog) {
 *                 // @var \IoT\Entity\Transports\HTTPServers\ThingsBoard
 *                $transport = $device->getTransport();
 *                if ( $transport->isDebugModeOn() ) $monoLog->error($transport->getDebugInfo());
 *            }
 *        );
 *        
 *        $i=0;
 *        $task = new RepeatableTask(
 *                function ($task, $logger) use (&$i, $softDevice ) {
 *                    $softDevice->run();
 *                    foreach($softDevice->getFields() as $field) $field->setValue();                    
 *                    if( $i >= 3 ) $task->stop();
 *                    $i++;
 *                    return(true);
 *                }, $monoLog, new RandomDelay(400,4000)
 *        );
 *        
 *        $task->run();
 * 
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class Device extends BaseAction implements \Serializable {
    //put your code here
    
    protected $attributes = null;
    
    protected $deviceAccessToken = '';
    
    public function __construct($accessToken) {
        parent::__construct();
        $this->setAccessToken($accessToken);
        $this->attributes = new Entity();
    }
    
    /**
     * 
     * @return \IoT\Entity\ThingsBoard\Device Itself
     */
    public function setAccessToken($accessToken){
        $this->deviceAccessToken= $this->testAccessToken($accessToken);
        return($this);
    }

    /**
     * Make validation of $accessToken. 
     * 
     * @param string $accessToken
     * @return string Given $accessToken if true otherwise throw exception
     * @throws Exception
     */
    protected function testAccessToken($accessToken){
        if( empty($accessToken) ) throw new Exception ('Given AccessToken is not valid.');
        return($accessToken);
    }

    /**
     * 
     * @return string Device's access token (similar to pointed in dashboard of ThingsBoard)
     */
    public function getAccessToken(){
        return($this->deviceAccessToken);
    }
    
    
    public function setAttribute(Field $attribute){
        return($this->_setFieldEntity($attribute, $this->attributes));
    }

    public function getAttributes(){
        return($this->_getFieldsEntity($this->attributes));
    }
    
    public function getAttribute($name){
        return($this->_getFieldEntity($name, $this->attributes));
    }

    public function removeAttribute(Field $attribute){
        return($this->_removeFieldEntity($attribute, $this->attributes));
    }
    
    public function clearAttributes(){
        return($this->_clearFieldEntity($this->attributes));        
    }

    public function run(){
        //validation of access token before run
        $this->testAccessToken($this->getAccessToken());
        
        $result = array(1,1);
        //send Data
        //@todo need think about how can to do more weak relation with class IoT\Entity\Transports\HTTPServers\ThingsBoard;
        //because REST\BaseAction allow any instances of ITransport -> setTransport(ITransport $transport = null)
        $this->getTransport()->setMethod('POST');
        //Need to process if count($this->getFields()) > 0 and  count($this->getAttributes()) == 0 ......
        if ( count($this->getFields()) > 0 && $this->_differentialRun('Device','fields') === false ) $result[0] = 0;
        if ( count($this->getAttributes()) > 0 && $this->_differentialRun('Device','attributes') === false ) $result[1] = 0;
        return( array_sum($result) === 2);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenValues(array $tokenNames) {
        $result = array();
        if ( count($tokenNames) === 1) {
            $result[$tokenNames[0]]=$this->deviceAccessToken;
        } else throw new \Exception('Current code support only one {ACCESS_TOKEN} other or null tokens not supported for \IoT\Entity\ThingsBoard\Device telemetry.');  

        return($result);
    }

    
    public function serialize() {
        return( serialize( array( parent::serialize(),serialize($this->attributes),serialize($this->getAccessToken()) ) ) );
    }
  
    
    public function unserialize($serialized) {
        list($serializedParent, $serializedAttributes, $serializedAccessToken ) = unserialize($serialized);
        parent::unserialize($serializedParent);
        
        $attributes = unserialize($serializedAttributes);
        if( $attributes === false ) $attributes = new Entity();
        $this->attributes = $attributes;

        $this->setAccessToken(unserialize($serializedAccessToken));
    }
}
