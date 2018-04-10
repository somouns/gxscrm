<?php
/**
 * Created by PhpStorm.
 * User: Xerxes Sultan
 * Date: 2017-09-25
 * Time: 15:47
 */

namespace app\admin\controller\product;


use app\common\controller\Backend;
use think\Controller;

class ProductReport extends Backend
{

    /**
     * 新增研报
     */
    public function add()
    {
        return $this->view->fetch();

    }

    /**
     * 编辑研报
     */
    public function edit()
    {
        return $this->view->fetch();

    }

    /**
     * 研报详情
     */
    public function detail()
    {
        return $this->view->fetch();

    }

    /**
     * 研报系列转发接口站点路由
     * Created by: Xerxes Sultan
     * 时间：2017年10月10日13:54:14
     */
    public function crmAdmin()
    {

    }

}