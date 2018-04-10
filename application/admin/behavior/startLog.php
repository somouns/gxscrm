<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-08-17
 * Time: 11:13
 */

namespace app\admin\behavior;
class startLog
{
    public function run(&$params)
    {
        dump($params);
    }
}