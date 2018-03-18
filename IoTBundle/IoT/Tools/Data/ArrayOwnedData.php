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

namespace IoT\Tools\Data;

/**
 * Generated: Dec 19, 2017 12:47:16 AM
 * 
 * Description of ArrayOwnedData
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class ArrayOwnedData implements IOwnedData {
    
    protected $owner = '';
    
    protected $data = null;
    
    public function __construct($owner = '') {
        $this->owner= strval($owner);
        $this->data = new \ArrayObject();
    }

    /**
     * 
     * @return string
     */
    public function getOwner() {
        return($this->owner);
    }

    protected function isOwnedBy($owner){
        $result = $this->getOwner() === strval($owner);
        if ( !$result ) {
            trigger_error('Owner of ArrayOwnedData not same as proposed \''.$owner.'\'. That is why nothing was did.', E_USER_WARNING);
        }
        return($result);
    }
    
    /**
     * 
     * @param string $name
     * @param string $owner
     * @return mixed Value that was stored before.
     */
    public function getValue($name, $owner = '') {
        $result= null;
        if ( $this->isOwnedBy($owner) ){
            $result= $this->data["$name"];
        }
        return($result);
    }

    /**
     * Set value in store.
     * 
     * @param string $name
     * @param mixed $value
     * @param string $owner
     * @return IOwnedData Itself
     */
    public function setValue($name, $value, $owner = '') {
        if ( $this->isOwnedBy($owner) ){
            $this->data["$name"]= $value;
        }
        return($this);
    }

    /**
     * Remove key -> value pair from data storage
     *  
     * @param string $name
     * @param string $owner
     * @return IOwnedData Itself
     */
    public function unsetValue($name, $owner = '') {
        if ( $this->isOwnedBy($owner) ){
            unset($this->data["$name"]);
        }
        return($this);
    }

    /**
     * 
     * {@inheritedoc}
     */
    public function getKeys() {
        return(array_keys($this->data));     
    }

}
