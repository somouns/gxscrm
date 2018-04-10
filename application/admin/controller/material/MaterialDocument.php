<?php

/**
 * 文档库控制器
 * Created by PhpStorm.
 * User: Xerxes Sultan
 * Date: 2017-10-09
 * Time: 14:46
 */
namespace app\admin\controller\material;

use app\common\controller\Backend;

class MaterialDocument extends Backend
{
    /**
     * 文档库列表
     * Created by: Xerxes Sultan
     * 时间：2017年10月9日14:57:20
     */
    public function index()
    {
        return $this->view->fetch();
    }

    /**
     * 文档库修改访问权限控制点
     * Created by: Xerxes Sultan
     * 时间：2017年10月9日15:50:51
     */
    public function edit()
    {
    }

    /**
     * 删除文档库的访问控制权限点
     * Created by: Xerxes Sultan
     * 时间：2017年10月9日15:50:56
     */
    public function delete()
    {
    }

    /**
     * 文档库系列接口转发接口站点方法
     * Created by: Xerxes Sultan
     * 时间：2017年10月10日13:54:14
     */
    public function crmAdmin()
    {

    }
}