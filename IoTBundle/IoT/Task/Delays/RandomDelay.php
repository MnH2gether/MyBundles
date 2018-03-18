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

namespace IoT\Task\Delays;

/**
 * Generated: Nov 14, 2017 11:19:27 PM
 * 
 * Description of RandomDelay
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class RandomDelay extends Delay {
    
    protected $minMillis = 500; // 1/2 sec
    
    protected $maxMillis = 30 * 60 * 1000; // 1/2 of hour
    
    public function __construct($minMillis, $maxMillis) {
        $maxRandomValue = mt_getrandmax();
        if ( $maxMillis * 1000 > $maxRandomValue )  throw new Exception ('Max value for RandomDelay is \''."$maxRandomValue".'\'');
//        if ( $maxMilis >= intval(PHP_INT_MAX / 1000) ) throw new Exception ('Max value for RandomDelay can\'t be more than \''.strval( intval(PHP_INT_MAX / 1000) ).'\'');
        if ( $minMillis < 0 )  throw new Exception ('Min value for RandomDelay can\'t be negative.');
        
        $this->maxMillis =  $maxMillis;
        $this->minMillis = $minMillis;
    }
    
    /**
     * {@inheritdoc}
     */
    public function delay() {
        $milliSec = mt_rand($this->minMillis, $this->maxMillis) ;
        usleep($milliSec * 1000);
        return($milliSec);
    }

}
