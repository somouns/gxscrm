<?php
/**
 * Created by PhpStorm.
 * User: Yyp
 * Date: 2017-10-10
 * Time: 15:47
 */

namespace app\admin\controller\group;


use app\common\controller\Backend;

class FileGroup extends Backend
{

    /**
     * 群组列表
     */
    public function index()
    {
        return $this->view->fetch();
    }

    /**
     * 新增群组
     */
    public function add()
    {
        return $this->view->fetch();
    }

    /**
     * 编辑群组
     */
    public function edit()
    {
        return $this->view->fetch();
    }

    /**
     * 查看群组
     */
    public function view()
    {
        return $this->view->fetch();
    }


    //-------------------------------------------------华丽的分割线------------------------------------------------------

    /**
     * 群组列表
     */
    public function indexs()
    {

    }

    /**
     * 新增群组
     */
    public function adds()
    {

    }

    /**
     * 编辑群组
     */
    public function edits()
    {

    }

    /**
     * 查看群组
     */
    public function views()
    {

    }


    /**
     * 删除群组
     */
    public function deletes()
    {

    }

}