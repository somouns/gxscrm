<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-10-09
 * Time: 17:19
 */

namespace app\admin\controller;



use fast\third\CallFunc;
use think\Controller;

class Test extends Controller
{
    public function test()
    {
        $result = CallFunc::task_call('15281057499');
        var_dump($result);exit;
    }
}