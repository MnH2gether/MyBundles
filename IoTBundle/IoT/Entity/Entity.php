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

namespace IoT\Entity;

use IoT\Entity\Fields\Field;
use IoT\Entity\Transports\ITransport;

/**
 * Generated: Nov 3, 2017 12:13:53 AM
 * 
 * Description of Entity
 * 
 * $softDevice = new Entity( new HTTPServers\ThingsBoard(null, 'http://localhost:8080/api/v1/hK46TABNw3fyvBRDj8qm/telemetry', 'GET') );
 * 
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class Entity implements IDataSource {
    //put your code here
    
    /**
     * Internal holder of instance of ITransport
     * @var \IoT\Entity\Transports\ITransport 
     */
    protected $transport = null;
    
    /**
     *
     * @var array 
     */
    protected $fields = array();
    
    /**
     * 
     * @param \IoT\Entity\Transports\ITransport $transport
     */
    public function __construct(ITransport $transport = null) {
        $this->setTransport($transport);
    }
    
    /**
     * 
     * 
     * @return array Returns array of IoT\Entity\Fields\Field
     */
    public function getFields() {
        return($this->fields);
    }

    /**
     * 
     * @param string $fieldName case sensitive
     * @return integer '-1' if field not found
     */
    public function indexFieldByName($fieldName) {
        $result = -1;
        for ($i=0; $i < count($this->fields); $i++ ) {
            if( $this->fields[$i]->getName() === $fieldName){
                $result= $i;
                break;
            }
        }
        return($result);
    }
        
    /**
     * 
     * @param \IoT\Entity\Fields\Field $field
     * @param boolean $removeSame will search and replace field with same name
     * @return \IoT\Entity\Entity Itself
     */
    public function setField(Field $field, $removeSame = true) {
        $idx = count($this->fields); 
        if( $removeSame ) {
            $idxSame = $this->indexFieldByName($field->getName());
            if ( $idxSame > -1 ) $idx = $idxSame; 
        }
        $this->fields[$idx]= $field;
        return($this);
    }
    
    /**
     * Clear list of fields
     */
    public function clearFields() {
        $this->fields = array();
    }
    
    /**
     * 
     * @param integer $index
     * @return \IoT\Entity\Entity Itself
     */
    public function removeField($index) {
        if ( $index > -1 && $index < count($this->fields) ) {
            unset($this->fields[$index]);
            $this->fields = array_filter($this->fields);
        }

        return($this);
    }
    
    /**
     * 
     * @return \IoT\Entity\Transports\ITransport
     */
    public function getTransport() {
        return($this->transport);        
    }

    /**
     * 
     * @param \IoT\Entity\Transports\ITransport $transport
     * @return \IoT\Entity\Entity Itself
     */
    public function setTransport(ITransport $transport = null) {
        $workTransport = $this->transport;
        $dataSource = $this;

        if ($this->transport !== $transport){
            $this->transport = $transport;
            if ($transport === null) $dataSource= null;
             else $workTransport = $transport;
        } else $workTransport = null;   
        
        if ( $workTransport !== null ) $workTransport->setDataSource($dataSource);     
        return($this);
    }

}
