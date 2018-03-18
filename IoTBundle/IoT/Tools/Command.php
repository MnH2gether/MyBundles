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

namespace IoT\Tools;

use IoT\Entity\IRunnable;

/**
 * Generated: Dec 10, 2017 7:51:34 AM
 * 
 * Description of Command
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class Command implements IRunnable{
    //put your code here
    
    protected $instance = null;
    
    protected $method = '';
    
    protected $params = array();
    
    protected $value = null;
    
    /**
     * Parameters that passing in constructor are intended only for class instantiation.
     * By other words they will be passed as parameters the constructor of class.
     * These parameters using each time when using {@see setClass()} method.
     * 
     * @param string|object $class
     * @param array $params
     */
    public function __construct($class, array $params = array()) {
        $this->setClass($class, $params);
        
    }
    
    public function getClass(){
        return($this->instance);
    }
    
    public function setClass($class, array $params = array() ){
        if ( is_object($class) ) {
            $this->instance = $class;
        } else if ( class_exists($class) ){
            $rc = new \ReflectionClass($class);
            $this->instance = $rc->newInstanceArgs($params);
        } else throw new Exception ('This class is not supported: \''."$class".'\'');

        return($this);
    }

    public function getMethod(){
        return $this->method;
    }
    
    public function setMethod($method){
        if ( !method_exists($this->getClass(), "$method") ) {
            throw new Exception ('Method does not exists: \''."$method".'\'');
        }
        
        $this->method = $method;
        return($this);
    }

    /**
     * Pay attention this parameters are intended for method of class as parameters of callback and
     * not same as parameters in constructor. Parameters that passing in constructor are intended only
     * for class instantiation. By other words they will be passed as parameters the constructor of class.
     *  
     * @return array Returns parameters for $method that were setted before by call the setParams() method.
     */
    public function getParams(){
        return $this->params;
    }
    
    /**
     * Parameters that need pass into method.
     *  
     * @param array $params
     * @return Command Itself
     */
    public function setParams(array $params){
        $this->params= $params;
        return($this);
    }

    /**
     * 
     * @return mixed Returns values that was returned after call the run() method.
     */
    public function getValue(){
        return $this->value;
    }
    
    /**
     * 
     * @param mixed $value
     * @return Command Itself 
     */
    protected function setValue($value){
        $this->value = $value;
        return($this);
    }
    
    /**
     * {@inheritdoc}
     */
    public function run() {
        $instance = $this->getClass();
        $callback = array($instance,$this->getMethod());
        if ( !is_callable( $callback ) ) {
            throw new Exception('\''.get_class($instance).'::'.$this->getMethod().'\' is not callable.');
        }
        $this->setValue( call_user_func_array( array($instance,$this->getMethod() ), $this->getParams()) );
        return($this->getValue());
    }

}
