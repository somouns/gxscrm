<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-08-31 08:37
 * Author: SteveGao
 * Company: Mr.Stock CRM Project Team
 */
$GLOBALS['baseconfig'] = \think\Config::get('predis'); //加载配置文件中的predis 集群配置

function C($key) {
    if (strpos($key,'.')){
        $key = explode('.',$key);
        if (isset($key[2])){
            return $GLOBALS['baseconfig'][$key[0]][$key[1]][$key[2]];
        }else{
            return $GLOBALS['baseconfig'][$key[0]][$key[1]];
        }
    }else{
        return $GLOBALS['baseconfig'][$key];
    }
}


