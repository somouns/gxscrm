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

class ProductSuggestcontent extends Backend
{

    /**
     * 新增话术
     */
    public function add()
    {
        return $this->view->fetch();

    }

    /**
     * 编辑话术
     */
    public function edit()
    {
        return $this->view->fetch();

    }

    /**
     * 话术详情
     */
    public function detail()
    {
        return $this->view->fetch();

    }


    /**
     * 话术系列接口转发接口站点方法
     * Created by: Xerxes Sultan
     * 时间：2017年10月10日19:14:25
     */
    public function crmAdmin()
    {

    }

}