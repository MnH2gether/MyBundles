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

/**
 * Generated: Nov 16, 2017 9:10:34 PM
 * 
 * Description of BaseAction
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
abstract class BaseAction extends BaseActionPrototype implements \Serializable{

    /**
     *
     * @var \IoT\Entity\Fields\IField
     */
    protected $fields = null;
    
    
    public function __construct() {
        parent::__construct();
        $this->fields = new Entity();
    }

    /**
     * 
     * @param Field $fields
     * @return \IoT\Entity\ThingsBoard\BaseAction Itself
     */
    public function setField(Field $fields){
        return($this->_setFieldEntity($fields, $this->fields));
    }

    public function getFields(){
        return($this->_getFieldsEntity($this->fields));
    }

    public function getField($name){
        return($this->_getFieldEntity($name, $this->fields));
    }

    public function removeField(Field $fields){
        return($this->_removeFieldEntity($fields, $this->fields));        
    }
    
    public function clearFields(){
        return($this->_clearFieldEntity($this->fields));        
    }
 
    public function serialize() {
        //we don't need invoke parrent serialization method because it nothing does.
        return(serialize($this->fields));
    }
    
    public function unserialize($serialized) {
        //Parent unserializer doesn't depends from $serialized value
        parent::unserialize(null);
        
        $fields = unserialize($serialized);
        if( $fields === false ) $fields = new Entity();
        $this->fields = $fields;
        
    }
    
}
