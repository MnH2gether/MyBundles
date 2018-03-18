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

/**
 * Generated: Nov 12, 2017 11:55:20 PM
 * 
 * Description of APIInfo
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class APIInfo {
    //put your code here
    
    const REST = array(
        /* version */ '1.3.1' =>array(
            'Device'=>array(
                'fields'=>'/api/v1/{ACCESS_TOKEN}/telemetry',
                'attributes'=> '/api/v1/{ACCESS_TOKEN}/attributes'
            ),
            'Login'=>array(
                'fields'=>'/api/auth/login'
            ),
            'TimeSeries'=>array(
                'keys'=>'/api/plugins/telemetry/{entityType}/{entityId}/keys/timeseries',
                'values'=>'/api/plugins/telemetry/{entityType}/{entityId}/values/timeseries',
                
            )
        ) ,
    );
    
    
    static public function getLastVersion() {
        $result = false;
        $keys = array_keys(self::REST);
        $cntKeys = count($keys);
        if ( $cntKeys > 0 ) {
            $result = $keys[0];
            if ( usort($keys, 'version_compare') ) {
                $result = $keys[$cntKeys-1];
            }
        }
        return($result);
    }
    
    
    static public function getValueByDottedKey($dottedKey,array $haystack){
        $result = false;
        $keys = explode('.', $dottedKey);
        $cntKeys = count($keys);

        $cur = $haystack;
        for ($i = 0; $i < $cntKeys; $i++){
            if ( is_array($cur) && array_key_exists($keys[$i], $cur) ){
                $cur = $cur[$keys[$i]];
            } else { break; }
            if ($i === $cntKeys-1) $result= $cur;
        }
        return($result);
    }
    
    /**
     * 
     * @param string $forWhat Keys in dot notation for nested keys of ::REST array
     * @param string $version
     * @return false|string
     */
    static public function getUrl($forWhat,$version=''){
        $result=false;
        $version = trim($version);
        if ( empty($version) ) $version = self::getLastVersion();
        if( array_key_exists($version, self::REST) ) {
            $result = self::getValueByDottedKey($forWhat,self::REST["$version"]);
        }
        return($result);
    }
    
    
}
