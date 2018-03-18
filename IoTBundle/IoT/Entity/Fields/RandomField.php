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

namespace IoT\Entity\Fields;

/**
 * Generated: Nov 1, 2017 12:12:48 AM
 * 
 * Description of RandomField
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class RandomField extends Field{
    //put your code here
    
    const nameLength = 8;
    
    const avaibleCharsInName = 'qazwsxedcrfvtgbyhnujmikolp';
    
    const avaibleCharsInRandomValue = 'qazwsxedcrfvtgbyhnujmikolp1234567890-=QAZXSWEDCVFRTGBNHYUJM<KIL>?:OP"{}|\\/.,~!@#$%^&*()_+~';
    
    protected $valueLimit = array('string'=>16, 'double'=>100, 'integer'=>100);
    
    
    public function __construct($name=null, $type=null, $value=null) {
        parent::__construct($name, $type, $value);
    }
    
    private function _getRandomString(array $avaibleChars, $resultLengs){
        $result= array();
        $resultLengs = intval($resultLengs);
        $maxIndxAvaibleChars = count($avaibleChars)-1;
        for ($i = 0; $i < $resultLengs; $i++){
           $idx= mt_rand(0,$maxIndxAvaibleChars);
           $result[] = $avaibleChars[$idx];
        }
        return(implode('',$result));
    }
    
    private function _generateName(){
        return($this->_getRandomString(str_split(self::avaibleCharsInName), self::nameLength));
    }
    
    public function setName($name) {
        $name = !empty($name) ? $name : $this->_generateName();
        return(parent::setName($name));
    }    
    
    private function _generateType(){
        return($this->_getRandomString(FieldTypes::IoTEntityFieldTypes, 1));
    }
    
    public function setType($type) {
        $type = !empty($type) ? $type : $this->_generateType();
        return(parent::setType($type));
    }    
    
    private function _generateString($length=null){
        $stringRandomValueLength = $this->getValueLimit($this->getType());
        $length = intval($length) ? intval($length) :  $stringRandomValueLength;
        return($this->_getRandomString(str_split(self::avaibleCharsInRandomValue), $length));
    }
    
    private function _getMaxValueForIntAndDouble(){
        $max = $this->getValueLimit($this->getType());
        if ( !$max ) $max = mt_getrandmax();        
        return ($max);
    }
    
    private function _generateInteger(){
        return(mt_rand(0,$this->_getMaxValueForIntAndDouble()));
    }

    private function _generateBoolean(){
        return( boolval( round(mt_rand(0, 1),0) ) );
    }

    private function _generateDouble(){
        $max = $this->_getMaxValueForIntAndDouble();
        return( doubleval( mt_rand(0,$max) * (mt_rand(0,$max) / $max) ) );
    }
    
    private function _generateValue(){
        $fn = '_generate'.ucfirst($this->getType());
        return($this->$fn());
    }
    
    public function setValue($value = null) {
        $value = $value !== null ? $value : $this->_generateValue();
        return(parent::setValue($value));
    }
    
    
    public function getValueLimit($type){
        $result = null;
        if ( array_key_exists($type, $this->valueLimit) ) $result= intval($this->valueLimit["$type"]);
        return($result);
    }

    
    public function setValueLimit($type, $limit){
        $limit = intval($limit);
        if ( FieldTypes::isValidType($type) ) {
            if ( array_key_exists($type, $this->valueLimit) ) $this->valueLimit["$type"] = $limit;
        }
        return($this);
    }
    
}
