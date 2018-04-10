<?php
/**
 * Created by PhpStorm.
 * User: Yyp
 * Date: 2017-10-10
 * Time: 15:47
 */

namespace app\admin\controller\examine;


use app\common\controller\Backend;

class Examine extends Backend
{

    /**
     * 质检客户列表
     */
    public function examine_list()
    {
        return $this->view->fetch();
    }

    /**
     * 质检全部审核记录
     */
    public function examine_record_list()
    {
        return $this->view->fetch('examine_list');
    }

    /**
     * 质检审核页面
     */
    public function examine_view()
    {
        return $this->view->fetch();
    }

    /**
     * 质检审核提交
     */
    public function examine_post()
    {
        return $this->view->fetch();
    }


//---------------------------------------------------------我就是要把你们分开！----------------------------------------------------------------------------


    /**
     * 质检客户列表
     */
    public function examine_lists()
    {

    }

    /**
     * 质检全部审核记录
     */
    public function examine_record_lists()
    {

    }

    /**
     * 质检审核页面
     */
    public function examine_views()
    {

    }

    /**
     * 质检审核提交
     */
    public function examine_posts()
    {

    }

}