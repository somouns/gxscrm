<?php

/**
 * Created by PhpStorm.
 * User: Xerxes Sultan
 * Date: 2017-10-17
 * Time: 16:25
 */
namespace app\admin\controller\call;

use app\common\controller\Backend;

class Call extends Backend
{

    public function index(){
        return $this->view->fetch();
    }

    public function mylist(){
        return $this->view->fetch();
    }

    public function bindlist(){
        return $this->view->fetch();
    }





    /**
     * 通话记录的列表页
     * Created by: Xerxes Sultan
     */
    public function lists()
    {
        return $this->view->fetch();
    }

    /**
     * 通话记录列表
     * Created by: Xerxes Sultan
     */
    public function listcall()
    {

    }


    /**
     * 拨打电话的权限点
     * Created by: Xerxes Sultan
     */
    public function call()
    {
    }

    /**
     * 删除通话记录的路由
     * Created by: Xerxes Sultan
     */
    public function delete()
    {

    }

    /**
     * 获取个人的最近通话列表
     * Created by: Xerxes Sultan
     */
    public function get()
    {

    }


    /**
     * 拨打电话调用接口站点转发的地址
     * Created by: Xerxes Sultan
     * 时间：2017年10月17日16:27:45
     */
    public function crmAdmin()
    {

    }
}