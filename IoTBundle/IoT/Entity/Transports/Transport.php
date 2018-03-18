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

namespace IoT\Entity\Transports;

use IoT\Entity\IDataSource;
/**
 * Generated: Nov 3, 2017 12:09:09 AM
 * 
 * Description of Transport
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
abstract class Transport implements ITransport {
    //put your code here
    
    protected $dataSource = null;
    
    protected $onConfig = null;
    
    /**
     * 
     * @param \IoT\Entity\IDataSource $dataSource
     */
    public function __construct(IDataSource $dataSource = null) {
        $this->setDataSource($dataSource)->boot();
    }

    /**
     * 
     * @param \IoT\Entity\IDataSource $dataSource
     * @return \IoT\Entity\Transports\Transport Itself
     * @throws Exception
     */
    public function setDataSource(IDataSource $dataSource = null) {
        $workDS = $this->dataSource;
        $transport = $this;

        if ($this->dataSource !== $dataSource){
            $this->dataSource = $dataSource;
            if ($dataSource === null) $transport= null;
            else $workDS = $dataSource;
        } else $workDS = null;   
        
        if ( $workDS !== null ) $workDS->setTransport($transport);     
        return($this);
    }
    
    /**
     * 
     * @return \IoT\Entity\IDataSource
     */
    public function getDataSource() {
        return($this->dataSource);
    }

    /**
     * {@inheritdoc}
     */
    abstract public function boot();
    
    /**
     * {@inheritdoc}
     */
    abstract public function run();
    
    /**
     * 
     * @return Callable
     */
    public function getOnConfig() {
        return($this->onConfig);
    }   
    
    /**
     * Must be invoked in appropriate place. As rule inside run() method and right before real run execution. 
     * 
     * @return null|mixed result of callback execution.
     */
    public function onConfig() {
        $result=null;
        $onConfigCallback = $this->getOnConfig();
        if ( $onConfigCallback !== null && is_callable($onConfigCallback) ) {
            $result = call_user_func ($onConfigCallback, $this);
        }
            
        return($result);
    }   
    
    /**
     * 
     * @param Callable $callback
     * @return \IoT\Entity\Transports Itself or descendants
     */
    public function setOnConfig(Callable $callback) {
        $this->onConfig = $callback;
        return($this);
    }   
}
