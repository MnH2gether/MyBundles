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

use IoT\Entity\Fields\Field;

/**
 * Generated: Nov 16, 2017 9:09:21 PM
 * 
 * Description of Login
 * 
 * $login = new Login('test@ttt.com','12qwaszx');
 * $loginToken= $login->run();
 * 
 * ..........
 * ..........
 * ..........
 * 
 * $login->getLastToken(); //same as $loginToken
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class Login extends BaseAction {
    //put your code here
    
    protected $lastToken = '';
    
    /**
     * 
     * @param string $userName
     * @param string $password
     */
    public function __construct($userName, $password) {
        parent::__construct();
        $this->setField(new Field('username', 'string', "$userName"))->setField(new Field('password', 'string', "$password"));
    }
    
    public function run(){
        $this->getTransport()->setMethod('POST');
        $result= $this->_differentialRun('Login','fields');
        if ( $result !== false ) {
            $result= @json_decode ($result);
            if( isset($result->token) ){
                //https://thingsboard.io/docs/reference/rest-api/
                //on the bottom of page >>> Now, you should set ‘X-Authorization’ to “Bearer $YOUR_JWT_TOKEN”
                $this->lastToken = $result= "Bearer ".$result->token;
            }
        }
        return($result);
    }

    /**
     * 
     * @return false|string 
     */
    public function getLastToken(){
        $result= false;
        if( !empty($this->lastToken) ) $result = $this->lastToken;
        return($result);
    }
    
    protected function getTokenValues(array $tokenNames ) {
        return(array());
    }

}
