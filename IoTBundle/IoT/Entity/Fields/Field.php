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

namespace IoT\Entity\Fields;

/**
 * Generated: Oct 31, 2017 11:36:30 PM
 * 
 * Description of Field
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class Field implements IField, \Serializable {
    //put your code here
    
    private $name = null;
    
    private $value = null;
    
    private $type = null;
    
    public function __construct($name, $type, $value) {
        $this->setName($name);
        $this->setType($type);
        $this->setValue($value);
    }
            
    
    public function getName() {
        return($this->name);
    }

    public function getType() {
        return($this->type);
    }

    public function getValue() {
        return($this->value);
    }

    public function setName($name) {
        $result= strtolower(trim("$name"));
        if( $result !== '' ) $this->name = $result;
          else throw new \Exception('Name of field must not be empty or only spaces.');
        return($this);
    }

    public function setType($type) {
        if ( FieldTypes::isValidType($type) ) $this->type = $type;
          else throw new \Exception('Used not supported field type. Type must be in list: \''.implode('\',\'',FieldTypes::IoTEntityFieldTypes)).'\'';
        return($this);
    }

    public function setValue($value) {
        if( FieldTypes::isValidValue($this->getType(),$value) ) $this->value= $value;
          else throw new \Exception('Used not appropriate value for this field of \''.$this->getType().'\' type.'); 
        return($this);
    }

    public function serialize() {
        return(serialize( array($this->name, $this->type, $this->value) ) );
    }

    public function unserialize($serialized) {
        list($this->name, $this->type, $this->value) = unserialize($serialized);
    }

}
