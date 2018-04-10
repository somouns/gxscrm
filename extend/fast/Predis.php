<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-09-01 16:16
 * Author: SteveGao
 * Company: Mr.Stock CRM Project Team
 */
namespace fast;
use think\Loader;

class Predis
{
    //初始化redis 集群文件
    public static function init()
    {
        Loader::import('predis.Pconfig', EXTEND_PATH); //加载predis 配置文件
        Loader::import('predis.CachePredis', EXTEND_PATH); // 加载predis 类库文件
        $redis = new \CachePredis();
        return $redis;
    }
}