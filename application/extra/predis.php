<?php
/**
 * redis 集群配置文件
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-09-01 16:04
 * Author: SteveGao
 * Company: Mr.Stock CRM Project Team
 */

$baseconfig = array();
$baseconfig['cache_open'] = true;
$baseconfig['redis_hashsharding'] = array('stocksir:questions','stocksir:policys','stocksir:comments','stocksir:bbs','stocksir:member:message','stocksir:member:sms');

$baseconfig['redis_config'] = array(
    'cluster'=>array(
        'prefix' => 'stocksir_',
        'type'=>'cluster',
        'seeds_nodes' =>array(
            '192.168.10.243:7001',
            '192.168.10.243:7001',
            '192.168.10.243:7002',
        ),
        'slaves'=>array(
            '5460'=>array(array('host'=>'192.168.10.243','port'=>7003,'pconnect'=>0,'db'=>0)),
            '10922'=>array(array('host'=>'192.168.10.243','port'=>7001,'pconnect'=>0,'db'=>0)),
            '16383'=>array(array('host'=>'192.168.10.243','port'=>7002,'pconnect'=>0,'db'=>0))
        ),
    ),
    'appcluster'=>array(
        'prefix' => 'stocksir_',
        'type'=>'clusterclient',
        'seeds_nodes' =>array(
            '192.168.10.243:7000',
            '192.168.10.243:7001',
            '192.168.10.243:7002',
        ),
        'master_groups'=>array(
            '5460' =>array(array('host'=>'192.168.10.243','port'=>7000,'pconnect'=>0),array('host'=>'192.168.10.243','port'=>7003,'pconnect'=>0)),
            '10922'=>array(array('host'=>'192.168.10.243','port'=>7001,'pconnect'=>0),array('host'=>'192.168.10.243','port'=>7004,'pconnect'=>0)),
            '16383'=>array(array('host'=>'192.168.10.243','port'=>7002,'pconnect'=>0),array('host'=>'192.168.10.243','port'=>7005,'pconnect'=>0))
        ),
        'slaves'=>array(
            '5460'=>array(array('host'=>'192.168.10.243','port'=>7000,'pconnect'=>0,'db'=>0)),
            '10922'=>array(array('host'=>'192.168.10.243','port'=>7001,'pconnect'=>0,'db'=>0)),
            '16383'=>array(array('host'=>'192.168.10.243','port'=>7002,'pconnect'=>0,'db'=>0))
        ),
    ),
    'appsentinel'=>array(
        'prefix' => 'stocksir_',
        'type'=>'sentinel',
        'sentinel_servers' =>array(
            array('192.168.10.243','26379'),
            array('192.168.10.243','26380'),
            array('192.168.10.243','26381'),
        ),
        'cluster_alias' =>array(
            '16383'=>'master1',
        ),
        'slaves'=>array(
            '16383'=>array(array('host'=>'192.168.10.243','port'=>6379,'pconnect'=>1,'db'=>0))
        ),
    ),
    'hq'=>array(
        'prefix' => 'stocksir_',
        'type'=>'redis',
        'master'=>array(array('host'=>'192.168.10.243','port'=>6379,'pconnect'=>0,'db'=>0)),
        'slave'=>array(array('host'=>'112.74.132.90','port'=>6479,'pconnect'=>0,'db'=>0),array('host'=>'112.74.132.90','port'=>6479,'pconnect'=>0,'db'=>0))
    ),
    'combine'=>array(
        'prefix' => 'stocksir_',
        'type'=>'redis',
        'master'=>array(array('host'=>'192.168.10.231','port'=>6379,'pconnect'=>0,'db'=>0)),
        'slave'=>array(array('host'=>'192.168.10.231','port'=>6379,'pconnect'=>0,'db'=>0),array('host'=>'192.168.10.231','port'=>6379,'pconnect'=>0,'db'=>0))
    ),
    'local'=>array(
        'prefix' => 'stocksir_',
        'type'=>'redis',
        'master'=>array(array('host'=>'192.168.0.222','port'=>6379,'pconnect'=>0,'db'=>0)),
        'slave'=>array(array('host'=>'192.168.0.222','port'=>6379,'pconnect'=>0,'db'=>0))
    ),

);

return $baseconfig;