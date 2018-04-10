<?php
/**
 * Created by PhpStorm.
 * User: Xerxes Sultan
 * Date: 2017-09-25
 * Time: 15:47
 */

namespace app\admin\controller\product;


use app\common\controller\Backend;

class Product extends Backend
{

    /**
     * 产品列表页
     */
    public function index()
    {
        return $this->view->fetch();
    }

    /**
     * 添加产品
     */
    public function add()
    {
        return $this->view->fetch();
    }

    /**
     * 编辑产品
     */
    public function edit()
    {
        return $this->view->fetch();
    }

    /**
     * 产品详情
     */
    public function detail()
    {
        return $this->view->fetch();
    }


    /**
     * 产品操作日志
     */
    public function log()
    {
        return $this->view->fetch();
    }

    /**
     * 产品系列接口转发接口站点方法
     * Created by: Xerxes Sultan
     * 时间：2017年10月10日13:54:14
     */
    public function crmAdmin()
    {

    }

    /**
     * 产品服务记录
     */
    public function servicerecord()
    {
        return $this->view->fetch();
    }

}