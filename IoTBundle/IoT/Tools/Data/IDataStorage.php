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
 * Generated: Dec 18, 2017 12:14:34 AM
 * 
 * Description of IDataStorage
 * 
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
interface IDataStorage {
    //put your code here

    /**
     * 
     * @param string|obj $namespace
     * @return IOwnedData Instance/container
     */
    public function getData($namespace = '');
    
    
    /**
     * Must create internal data storage that implements IOwnedData interface.
     * 
     * @param string|object $namespace
     * @return ArrayDataStorage Itself
     */
    public function setData($namespace = '');
    
    
    /**
     * Must remove container for $namespace reference.
     * 
     * @param string|object $namespace
     * @return ArrayDataStorage Itself
     */
    public function unsetData($namespace = '');
    
    
    
    /**
     * Must return value from internal data storage that implements IOwnedData by $name
     * 
     * @param string $name 
     * @param string|obj $namespace
     */
    public function getValue($name, $namespace = '');
    

    /**
     * This method must set data that associated with certain namespace and name but without access
     * to storage/container like ArrayObject. By other words It must not have possibility to change
     * main storage container. But if any container for $namespace doesn't exists then need to create
     * container using setData() method.
     * 
     * @param string $name Certain name of value in $namespace
     * @param mixed $value Value
     * @param string|obj $namespace Namespace.
     */
    public function setValue($name, $value, $namespace = '');
    
    
    /**
     * This must invoke in all method of this interface for getting actual name space.
     * Also It can be invoked from out for get full specified string representation for $namespace
     * 
     * @param string|object $namespace If string the must return itself.
     * @return string
     */
    public function getOwnedNamespace($namespace);
    
}
