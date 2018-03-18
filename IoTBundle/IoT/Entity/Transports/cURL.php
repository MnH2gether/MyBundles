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

/**
 * Generated: Nov 3, 2017 5:49:50 AM
 * 
 * Description of cURL
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
abstract class cURL extends Transport {
    //put your code here
    
    protected $handle = null;
    
    protected $url = null;
    
    protected $debugMode = false;
    
    protected $debugInfo = '';
    
    public function __construct(\IoT\Entity\IDataSource $dataSource = null, $url = null) {
        $this->init();
        parent::__construct($dataSource);
        $this->setUrl($url);
    }

    /**
     * Make reinitialization of cURL handler
     * 
     * @return IoT\Entity\Transports\cURL Itself
     * @throws Exception if cURL can't be initialized.
     */
    public function init(){
        $handle = $this->getHandler();
        if ( $handle !== null && is_resource($handle) ) curl_close($handle);
        $ch = curl_init();
        if ($ch === false) throw new \Exception('Can\'t initialize cURL transport.'); 
        $this->handle = $ch;
        return($this);
    }
    
    public function boot() {
        //if success will returned real answer of server.  
        curl_setopt($this->getHandler(), CURLOPT_RETURNTRANSFER, true);
    }
    
    public function onConfig(){
        parent::onConfig();
        if ( !curl_setopt($this->getHandler(), CURLOPT_URL,$this->getUrl()) ) throw new Exception ('Can\'t set URL in cURL transport in \''.$url.'\'');
    }
    
    /**
     * It must set up an URL for transport location in depends from protocol.
     * for example:
     * coap://host:port/api/v1/$ACCESS_TOKEN/telemetry
     * mqtt://host:port/v1/devices/me/telemetry
     * http(s)://host:port/api/v1/$ACCESS_TOKEN/telemetry
     * 
     * @param string $url
     * @return IoT\Entity\Transports\cURL Itself
     */
    public function setUrl($url) {
        $this->url = trim(strval($url));
        return($this);
    }

    /**
     * 
     * @return string
     */
    public function getUrl() {
        return($this->url);
    }

    /**
     * Will execute right before curl_exec(). If will returned False then execution will stopped.
     * 
     * @param array $data Array that are copy of internal storage of DataSource.
     * You can change behaviour by default through redefining the protected getData() method.
     * 
     * @return boolean Should return True if action don't need termination otherwise False
     */
    abstract protected function beforeRun($data);

    /**
     * By default debugMode is false. This value can be setted in descendants. 
     * @return boolean Only if $this->debugMode property is True then will returned True otherwise -> False 
     */
    public function isDebugModeOn(){
        return( $this->debugMode === true );
    }
    
    private function _cUrlExecute(){
        $this->debugInfo='';
        
        $ch = $this->getHandler();
        if( $this->isDebugModeOn() ){
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            //Switching into 'php://output' stream does not works 'php://memory' too (Win10Pro 64b) - have curl error
            //$fh = fopen('php://output', 'w');
            //if ($fh !== false) curl_setopt($ch, CURLOPT_STDERR, $fh);
            
            /** BEGIN for debugging purposes only **/  
            $fh = false;
            $out = false;
            $tempfname = tempnam(sys_get_temp_dir(), 'IoTcURLTransportDebugLog');
            if ($tempfname !== false) {
                $fh= fopen($tempfname, 'w+b');
                if ($fh !== false) $out = $fh;
            } else $out= STDOUT; //Works good always
            /** END for debugging purposes only **/  
            
            curl_setopt($ch, CURLOPT_STDERR,$out);
        } else {
            curl_setopt($ch, CURLOPT_VERBOSE, false);
            curl_setopt($ch, CURLOPT_STDERR, STDERR);        
        }

        $result= curl_exec($ch);
               
        /** BEGIN for debugging purposes only **/        
        if ( $fh !== false ) {
            $sseked= fseek($fh, 0);
            while ( !feof($fh)  ) {
                $this->debugInfo .= fread($fh, 8192);
            }
            ftruncate($fh,0);
            fclose($fh);
        }
        if ( $tempfname !== false && file_exists($tempfname) ) unlink($tempfname);
        /** END for debugging purposes only **/  
        
        return($result); 
    }
    
    /**
     * Will get data from current DataSource as array of pairs key->value
     * @return array
     */
    protected function getData(){
        $result= array();
        $dSource = $this->getDataSource();
        if($dSource !== null){
            foreach($dSource->getFields() as $field){
                $result[$field->getName()] = $field->getValue();
            }
        }
        return($result);
    }
    
    
    /**
     * 
     * @return False|ResultOfExecution Returns result of curl_exec() where options CURLOPT_RETURNTRANSFER was set up in true.
     * If execution was fail then result will boolean False.
     */
    public function run() {
        $result = false;
        $this->onConfig();        
        $terminate = $this->beforeRun($this->getData());
        if( $terminate !== false) {
            $result= $this->_cUrlExecute();
        }
        return($result);
    }
    
    /**
     * For execution some cURL additional action.
     * 
     * @return resource Returns current cURL's handle. Don't close this handle manually using curl_close().
     */
    public function getHandler(){
        return($this->handle);       
    }
    
    public function __destruct() {
        $handle = $this->getHandler();
        if ( $handle !== null && is_resource($handle) ) {
            curl_close( $handle );
        }
    }
    
    
    
}
