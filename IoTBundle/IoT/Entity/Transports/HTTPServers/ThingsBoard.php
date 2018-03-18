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

namespace IoT\Entity\Transports\HTTPServers;

use IoT\Entity\Transports\cURL;

/**
 * Generated: Nov 3, 2017 7:12:50 AM
 * 
 * Description of ThingsBoard
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class ThingsBoard extends cURL{
    
    private $ennableLocalDebugOutput = false;
    
    protected $method = '';
    
    public function __construct(\IoT\Entity\IDataSource $dataSource = null, $url = null, $method = 'POST') {
        parent::__construct($dataSource, $url);
        $this->setMethod($method);
    }
    
    public function getMethod(){
        return($this->method);
    }

    protected function onPOST($data){
        $result= array();
        $result[] = (int) curl_setopt($this->getHandler(), CURLOPT_POST, true);
        $result[] = (int) curl_setopt($this->getHandler(), CURLOPT_POSTFIELDS, json_encode($data));
        $result[] = (int) curl_setopt($this->getHandler(), CURLOPT_HTTPHEADER, array("Content-Type:application/json"));
        return( array_sum($result) == 3 );
    }

    protected function onGET(){
        $result= array();
        $result[] = (int) curl_setopt($this->getHandler(), CURLOPT_HTTPGET, true);
        return( array_sum($result) == 1 );
    }

    
    protected function checkMethod($method){
        $method= strtoupper(trim("$method"));
        $allowMethods = array('GET','POST');
        if( !in_array($method,$allowMethods) ) {
            throw new Exception('You are trying not supported HTTP method: \''.$method.'\'');
        }
        return( array($this,'on'."$method") );
    }
    
    /**
     * 
     * @param type $method
     * @return type
     */
    public function setMethod($method){
        $this->method = $method;
        return($this);
    }

    
    //https://thingsboard.io/docs/reference/http-api/    
    //# Publish data as an object without timestamp (server-side timestamp will be used)
    //curl -v -X POST -d @telemetry-data-as-object.json http://localhost:8080/api/v1/$ACCESS_TOKEN/telemetry --header "Content-Type:application/json"
    //@telemetry-data-as-object.json contains
    //{"key1":"value1", "key2":"value2"}
    //or
    //[{"key1":"value1"}, {"key2":"value2"}]
    //Please note that in this case, the server-side timestamp will be assigned to uploaded data!
    //In case your device is able to get the client-side timestamp, you can use following format:
    //{"ts":1451649600512, "values":{"key1":"value1", "key2":"value2"}}
    
    protected function beforeRun($data) {
        $result= false;
        $callback = $this->checkMethod($this->getMethod());
        if (is_callable($callback)){
            $refM = new \ReflectionMethod($callback[0], $callback[1]);
            $refM->setAccessible(true);
            $refMParams = $refM->getParameters();
            $params = array();
            foreach ( $refMParams as $param) {
                if ( $param->getName() === 'data' ) $params[]= $data;
            }
            $result= $refM->invokeArgs($callback[0], $params);
        }
        
        return($result);
    }

    public function onConfig() {
        parent::onConfig();
        $host = parse_url($this->getUrl(),PHP_URL_HOST);
        $this->debugMode = ( $host  === 'localhost' || '127.0.0.1' ) ;
   }
    
    public function ennableLocalDebugOutput($enable = true){
        $this->ennableLocalDebugOutput = (boolean) $enable;
        return($this);
    }
    
    public function getDebugInfo(){
        return($this->debugInfo);
    }
    
    public function run() {
        $result= parent::run();
        if ($result !== false) {
            $httpResponceCode = curl_getinfo($this->getHandler(), CURLINFO_HTTP_CODE);
            if ( false !== $httpResponceCode && intval($httpResponceCode) / 100 <> 2 ){
                $this->debugInfo .= "Response body: \r\n".$result;
                $result = false;
            }
        }
        if ( $this->debugMode && $this->ennableLocalDebugOutput === true ) print($this->getDebugInfo()."\r\n");
        
        return($result);
    }    
    
}
