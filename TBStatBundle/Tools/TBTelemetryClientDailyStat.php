<?php

/*
 * Copyright (c) 2018, Semyon Mamonov <semyon.mamonov@gmail.com>.
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

namespace TBStatBundle\Tools;

/**
 * Generated: Mar 18, 2018 8:40:17 AM
 * 
 * Description of TBTelemetryClientDailyStat
 *
 * Returns data of statistical values from ThingsBoard "ts_kv" table that aggregated by day.
 * Daily statistical values (summarized by each day).
 * 
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class TBTelemetryClientDailyStat extends TBTelemetryClient{
    
    //put your code here
    
    /**
     * 
     * {@inheritdoc}
     * 
     */
    protected function getSQLStatement($where, array $params) {
        return 'SELECT "entity_type" AS "entity_type", "entity_id" AS "entity_id", "ts"/1000/60/60/24 AS "epoch_day"'.
               ', COUNT(*) AS "rows_affected", "key" AS "field_name", SUM(COALESCE("dbl_v", 0)) AS "field_value"'."\r\n".
               'FROM ts_kv'."\r\n".
               $where."\r\n".
               'GROUP BY "entity_type", "entity_id", "key", "ts"/1000/60/60/24';
    }

}
