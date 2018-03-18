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

namespace IoT\Task;

use IoT\Task\Delays\IDelay;
use Psr\Log\LoggerInterface;

/**
 * Generated: Nov 15, 2017 12:45:13 AM
 * 
 * Description of RepeatableTask
 *
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class RepeatableTask extends Task{
    
    protected $delay = null;

    protected $needsStop = false;
    
    
    public function __construct(Callable $callback, LoggerInterface $logger=null, IDelay $delay=null) {
        parent::__construct($callback, $logger);
        $this->delay = $delay;
    }
    
    
    public function run() {
        $counter = 0;
        do{
            $result = parent::run();
            $this->sendToLogger('it was step #'."$counter");
            $delayTime = $this->delay->delay();
            $this->sendToLogger('delay \''."$delayTime".' millisec.\' after step #'."$counter".' finished.');
            $counter++;
        } while ( !$this->isStoped() && $result !== false );
    }

    protected function isStoped(){
        return($this->needsStop);
    }
    
    public function stop() {
        $this->needsStop = true;
        return($this);
    }
    
    
}
