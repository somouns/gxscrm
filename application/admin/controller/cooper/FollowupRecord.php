<?php
/**
 * Created by PhpStorm.
 * User: Yyp
 * Date: 2017-10-10
 * Time: 15:47
 */

namespace app\admin\controller\cooper;


use app\common\controller\Backend;

class FollowupRecord extends Backend
{

    /**
     * 查看跟进记录
     */
    public function index()
    {
        return $this->view->fetch();
    }

    /**
     * 新增跟进记录
     */
    public function add()
    {
        return $this->view->fetch();
    }

    /**
     * 编辑跟进记录
     */
    public function edit()
    {
        return $this->view->fetch();
    }

    /**
     * 删除跟进记录
     */
    public function delete()
    {
        return $this->view->fetch();
    }


    //-----------------------------------------------------以下是接口--------------------------------------------------------

    /**
     * 查看跟进记录
     */
    public function indexs()
    {

    }

    /**
     * 新增跟进记录
     */
    public function adds()
    {

    }

    /**
     * 编辑跟进记录
     */
    public function edits()
    {

    }

    /**
     * 删除跟进记录
     */
    public function deletes()
    {

    }

    public function follow_type()
    {

    }

}