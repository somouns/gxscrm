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

class ProductTransfer extends Backend
{

    /**
     * 新增调仓记录
     */
    public function add()
    {
        return $this->view->fetch();

    }

    /**
     * 编辑调仓记录
     */
    public function edit()
    {
        return $this->view->fetch();

    }

    /**
     * 调仓记录详情
     */
    public function detail()
    {
        return $this->view->fetch();
    }

    /**
     * 调仓系列接口转发接口站点方法
     * Created by: Xerxes Sultan
     * 时间：2017年10月10日18:22:14
     */
    public function crmAdmin()
    {

    }

}