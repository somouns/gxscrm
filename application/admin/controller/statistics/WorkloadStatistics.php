<?php

namespace app\admin\controller\statistics;

use app\common\controller\Backend;


/**
 * [index 工作量统计]
 * @Author   W_wang
 * @email    1352255400@qq.com
 * @DateTime 2017-11-14T17:06:30+0800
 * @return   [type]                   [description]
 */
class WorkloadStatistics extends Backend
{

	/**
	 * [index description]
	 * @Author   W_wang
	 * @email    1352255400@qq.com
	 * @DateTime 2017-11-14T17:06:30+0800
	 * @return   [type]                   [description]
	 */
    public function index()
    {
        return $this->view->fetch();
    }

}