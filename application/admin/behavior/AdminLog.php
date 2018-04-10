<?php

namespace app\admin\behavior;

use think\Config;

class AdminLog
{

    public function run(&$params)
    {
        \app\admin\model\AdminLog::record();
    }

}
