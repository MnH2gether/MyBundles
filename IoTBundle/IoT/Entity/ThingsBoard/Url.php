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

/**
 * Generated: Nov 15, 2017 11:29:52 PM
 * 
 * Description of Url
 *
 * You should be careful, default port will have 8080 value. If you do not specify it explicitly in constructor.
 * But you still will possible remove Port part from Url through invoke setPort('');
 * 
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class Url implements IUrl {
    
    protected $scheme = 'http'; //ThingsBoard default REST API scheme. Will immediately redefined in constructor through setter.
    
    protected $host = 'localhost'; //ThingsBoard default REST API host on local machine. Will immediately redefined in constructor through setter.
    
    protected $port = '8080'; //ThingsBoard default REST API port. Will immediately redefined in constructor through setter.
    
    protected $path = '';
    
    protected $queryData = array();
        
    public function __construct($path='', $host='', $scheme = '', $port='', $queryData = array()) {
        if ($port === '') $port = '8080';
        $this->setPath($path)->setHost($host)->setScheme($scheme)->setPort($port)->setQuery($queryData);
    }
    
    private function _rmUnnecessary($str){
        return(strtolower(trim(strval($str))));
    }    
    
    public function assemblyUrl() {
        $portStr = $this->getPort();
        if (!empty($portStr)) $portStr = ':'.$portStr;
        $result= $this->getScheme().'://'.$this->getHost().$portStr.$this->getPath();
        $query = $this->getQuery();
        if ( !empty($query) ) $result .= '?'.$query;
        return($result);
    }

    /**
     * 
     * @param string $scheme
     * @return \IoT\Entity\ThingsBoard\Url Itself
     */
    public function setScheme($scheme) {
        $this->scheme= $this->_rmUnnecessary($scheme);
        if(empty($this->scheme) ) $this->scheme = 'http';
        return($this);
    }
    
    public function getScheme() {
        return($this->scheme);
    }

    /**
     * 
     * @param type $host
     * @return \IoT\Entity\ThingsBoard\Url Itself
     */
    public function setHost($host) {
        $this->host = $this->_rmUnnecessary($host);
        if(empty($this->host) ) $this->host = 'localhost';
        return($this);
    }
    
    public function getHost() {
        return($this->host);
    }


    /**
     * 
     * @param type $path
     * @return \IoT\Entity\ThingsBoard\Url Itself
     */
    public function setPath($path) {
        $this->path = trim(strval($path));
        if(!empty($this->path) && $this->path[0] !== '/') $this->path = '/'.$this->path;
        return($this);
    }
    
    public function getPath() {
        return($this->path);
    }

    /**
     * 
     * @param type $port
     * @return \IoT\Entity\ThingsBoard\Url Itself
     */
    public function setPort($port) {
        $port = $this->_rmUnnecessary($port);
        if( $port === '' || ( is_numeric($port) && intval($port) < 65536 && intval($port) > 0 ) ) $this->port = $port;
         else $this->port = '8080';
        return($this);
    }
    
    public function getPort() {
        return($this->port);
    }

    /**
     * 
     * @param array $data
     * @return \IoT\Entity\ThingsBoard\Url Itself
     * 
     */
    public function setQuery(array $data) {
        $this->queryData = $data;
        return($this);
    }
    
    
    public function getQuery() {
        $result = '';
        if ( is_array($this->queryData) && count($this->queryData) > 0 ) {
            if ( function_exists('http_build_query') ) {
                $result = http_build_query($this->queryData);
            } else {
                foreach ($this->queryData as $key=>$value){
                    $result .= rawurlencode($key).'='.rawurlencode($value).'&';
                }
                if ($result !== '' && substr($result, -1) === '&')  $result = substr ($result, 0, -1);
            }
        }
        return($result);
    }

}
