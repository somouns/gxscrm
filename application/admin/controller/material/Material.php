<?php

/**
 * 资料库控制器
 * Created by PhpStorm.
 * User: Xerxes Sultan
 * Date: 2017-10-09
 * Time: 14:46
 */
namespace app\admin\controller\material;

use app\common\controller\Backend;

class Material extends Backend
{
    /**
     * 资料库列表
     * Created by: Xerxes Sultan
     * 时间：2017年10月9日14:57:27
     */
    public function index()
    {
        return $this->view->fetch();
    }

    /**
     * 资料库的操作日志
     * @return string
     * Created by: Xerxes Sultan
     * 时间：2017年10月9日15:35:34
     */
    public function log()
    {
        return $this->view->fetch();
    }

    /**
     * 资料库编辑的访问控制权限点
     * Created by: Xerxes Sultan
     * 时间：2017年10月9日15:49:23
     */
    public function edit(){

    }

    /**
     * 删除资料库的访问权限控制点
     * Created by: Xerxes Sultan
     * 时间：2017年10月9日15:49:27
     */
    public function delete(){

    }

    /**
     * 资料库系列接口转发接口站点方法
     * Created by: Xerxes Sultan
     * 时间：2017年10月10日13:54:14
     */
    public function crmAdmin()
    {

    }
}