<?php
/**
 * Created by PhpStorm.
 * User: Xerxes Sultan
 * Date: 2017-09-25
 * Time: 15:47
 */

namespace app\admin\controller\product;


use app\common\controller\Backend;

class ProductStock extends Backend
{

    /**
     * 标的列表页
     */
    public function index()
    {
        return $this->view->fetch();
    }

    /**
     * 新增标的
     */
    public function add()
    {
        return $this->view->fetch();
    }

    /**
     * 编辑标的
     */
    public function edit()
    {
        return $this->view->fetch();

    }

    /**
     * 标的详情
     */
    public function detail()
    {
        return $this->view->fetch();

    }
    /**
     * 标的删除
     */
    public function delete()
    {
        return $this->view->fetch();

    }
    /**
     * 标的出局
     */
    public function out()
    {
        return $this->view->fetch();

    }
    /**
     * 产品标的转发接口站点路由方法
     * Created by: Xerxes Sultan
     * 时间：2017年10月10日15:43:32
     */
    public function crmAdmin()
    {

    }
}