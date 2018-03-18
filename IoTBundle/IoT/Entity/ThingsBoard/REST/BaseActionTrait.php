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

namespace IoT\Entity\ThingsBoard\REST;


use IoT\Entity\Transports\HTTPServers;
use IoT\Entity\Transports\ITransport;

/**
 * Generated: Nov 22, 2017 1:12:42 AM
 * 
 * Description of BaseActionTrait
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
trait  BaseActionTrait {
    
    /**
     * @return array Key => Value paired array where keys are elements from $tokenNames and values are appropriate added  values.
     */
    abstract protected function getTokenValues(array $tokenNames);
    
    
    private function _replaceTokens($rawPath, array $tokens){
        $tokenNames= array_keys($tokens);
        $tokenValues= array_values($tokens);
        return(str_replace($tokenNames, $tokenValues, "$rawPath"));
    }
    
    /**
     * 
     * @param string $pathTo Possible values must be correlated with \IoT\Entity\ThingsBoard\APIInfo::REST exclude version/ For example'Device.fields' or 'Device.attributes'.
     * @param string $version
     * @return string
     */
    protected function assemblyUrl($pathTo, $version=''){
        $result = ''; 
        $pathTo= trim("$pathTo");
        if ( !empty($pathTo) ){

            $rawPath = APIInfo::getUrl($pathTo,$version);
            if ( $rawPath !== false ){
                $tokenNames = array();
                preg_match_all('/(?i)({[[:alpha:]_]+})/i',$rawPath, $tokenNames); 
                $tokens = $this->getTokenValues($tokenNames[1]);
                if( is_array($tokens) && count($tokens)>0 ){
                    $rawPath = $this->_replaceTokens($rawPath,$tokens);
                }
                if ( !empty($rawPath) ) {
                    $result= $rawPath;
                } else throw new \Exception('URL for \''.$pathTo.'\' from \IoT\Entity\ThingsBoard\APPInfo::REST base after transformation is empty. See getTokenValues() method.');
            } else throw new \Exception('Appropriate URL for \''.$pathTo.'\' not found in \IoT\Entity\ThingsBoard\APPInfo::REST base.');

        }
        return( $this->getUrl()->setPath($result)->assemblyUrl() );
    }

    
    public function setTransport(ITransport $transport = null) {
        if ($transport !== null ){
            $this->transport = $transport;
        } else if($this->getTransport() === null ){
            $this->setTransport(new HTTPServers\ThingsBoard());
        }
        return($this);
    }
    
}
