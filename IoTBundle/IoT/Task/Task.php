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

namespace IoT\Task;

use Psr\Log\LoggerInterface;

/**
 * Generated: Nov 15, 2017 1:17:55 AM
 * 
 * Description of Task
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class Task implements ITask {

    protected $callback = null;
            
    protected $logger = null;   
    
    protected $name = '';
    
    public function __construct(Callable $callback, LoggerInterface $logger=null) {
        $this->callback = $callback;
        $this->setLogger($logger);     
        $this->setName();
    }
            
    protected function setLogger(LoggerInterface $logger){
        $this->logger = $logger;
        return($this);
    }    
    
    /**
     * default value is result of spl_object_hash() for current object
     * 
     * @param string $name
     * @return Task Itself
     */
    protected function setName($name = ''){
        if ($name === '') $this->name = spl_object_hash($this);
         else $this->name = $name;
        return ($this);
    }
    
    /**
     * 
     * @return string
     */
    protected function getLoggerMessagePrefix(){
        return('['.date('Y-m-d H:i:s').'] Task \''.$this->getName().'\' -> ');
    }
    
    
    protected function sendToLogger($message){
        $logger = $this->getLogger();
        if($logger != null) {
            $logger->info($this->getLoggerMessagePrefix().$message);
        }
        return($this);
    }
    
    public function run() {
        $this->sendToLogger('started.');
        $result = call_user_func($this->callback, $this, $this->getLogger());
        if ($result === false) $this->sendToLogger('result execution of task did return False.');
        $this->sendToLogger('finished.');
        return( $result );
    }

    /**
     * 
     * @return Psr\Log\LoggerInterface Instance
     */
    public function getLogger(){
        return($this->logger);
    }

    public function getName() {
        return($this->name);
        
    }

}
