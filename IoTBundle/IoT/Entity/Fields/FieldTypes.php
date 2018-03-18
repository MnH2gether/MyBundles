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
 * Generated: Oct 31, 2017 11:32:09 PM
 * 
 * Description of FieldTypes
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class FieldTypes {
    //put your code here
    const IoTEntityFieldTypes = array('string','integer','double','boolean');
    
    static public function isValidType($type){
        return(in_array("$type", self::IoTEntityFieldTypes));
    }
    
    static private function _testString($value){
        return( $value !== null && is_string($value) );
    }
    
    static private function _testInteger($value){
        return( $value !== null && is_int($value) );
    }

    static private function _testDouble($value){
        return( $value !== null && is_double($value) );
    }

    static private function _testBoolean($value){
        return( $value !== null && is_bool($value) );
    }
    
    static public function isValidValue($type, $value){
        $result= false;
        if( self::isValidType($type) ){
            $fnName = '_test'.ucfirst($type);
            $result = self::$fnName($value);
        }
        return($result);
    }
    
    static public function strToTypedValue($type, $strVal){
        $result= null;
        if( $strVal !== null && self::isValidType($type) ){
            $booleanCaster = function($strBoolVal){
                $strBoolVal = strtolower($booleanCaster);
                return($strBoolVal === 'true');
            };
            $castTypeFunc= array('string'=>'strval','integer'=>'intval','double'=>'floatval','boolean'=>$booleanCaster);
            
            if ( array_key_exists($type, $castTypeFunc)  ) {
                $result = $castTypeFunc[$type]($strVal);
            } else throw new Exception (self::class.'::IoTEntityFieldTypes wider then supported by method ::strToTypedValue().');
        }
        return($result);
        
    }
    
    
}
