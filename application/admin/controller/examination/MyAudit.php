<?php
/**
 * Created by PhpStorm.
 * User: Yyp
 * Date: 2017-10-10
 * Time: 15:47
 */

namespace app\admin\controller\examination;


use app\common\controller\Backend;

class MyAudit extends Backend
{

    /**
     * 我提交的审批
     */
    public function my_submit()
    {
        return $this->view->fetch('all_list');
    }

    /**
     * 待我修改的审批
     */
    public function my_modify()
    {
        return $this->view->fetch('all_list');
    }

    /**
     * 全部审批
     */
    public function all_list()
    {
        return $this->view->fetch('all_list');
    }

    /**
     * 删除合作情况
     */
    public function del_cooper(){
        return $this->view->fetch();
    }

    //---------------------------------------------------------华丽的分割线-----------------------------------------------------------------------


    /**
     * 我提交的审批
     */
    public function my_submits()
    {
    }

    /**
     * 待我修改的审批
     */
    public function my_modifys()
    {
    }

    /**
     * 全部审批
     */
    public function all_lists()
    {
    }


    /**
     * 删除合作情况
     */
    public function del_coopers(){
    }
}