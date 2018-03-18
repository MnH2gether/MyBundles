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
 * Generated: Dec 18, 2017 12:05:17 AM
 * 
 * Description of IOwnedData
 * 
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
interface IOwnedData {
    //put your code here
    
    /**
     * Owner must be assigned only once when instance of object that implements IOwnedData interface
     * is creating. If remove this method of interface (may be in future) It will add some security.
     * 
     * @return string String that identifies something what may be owner of data.
     */
    public function getOwner();
    
    /**
     * 
     * Returns value by name/key from instance ArrayObject or other that can be data's container or null
     * if $owner not same as owner. This don't add additional security but make access to data more safety.
     * I think it let avoid some mistakes.
     * Sure you can make something like ownedDataObject->getValue($name, ownedDataObject->getOwner());
     * but it will be consciously did.
     * 
     * @param string $name Or key that identifies value.
     * @param string $owner string which identifies who requests data.
     *  
     * @return mixed Any data.
     */
    public function getValue($name, $owner = '');
    
 
    /**
     * Must do something (trigger the warning) for inform that setted owner not same
     * as $owner if this case happens. Otherwise just set appropriate value into storage.
     * 
     * @param string $name Or key that identifies value.
     * @param mixed $value Value.
     * @param string $owner It must be any unique value which identifies who wants set data into storage.
     * 
     * @return IOwnedData Returns reference at itself.
     */
    public function setValue($name, $value, $owner = '');
    
    /**
     * Must do something (trigger the warning) for inform that setted owner not same
     * as $owner if this case happens. Otherwise just remove appropriate value from storage.
     * 
     * @param string $name Or key that identifies value.
     * @param string $owner It must be any unique value which identifies who wants set data into storage.
     * 
     * @return IOwnedData Returns reference at itself.
     */
    public function unsetValue($name, $owner = '');
    
    
    /**
     * Must return all accessible keys in this storage.
     * 
     * @return array.
     */
    public function getKeys();
    
}
