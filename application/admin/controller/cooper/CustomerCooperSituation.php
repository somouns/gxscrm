<?php

namespace app\admin\controller\cooper;

use app\common\controller\Backend;
use EasyWeChat\Staff\Session;
use fast\Predis;
use think\Loader;
use think\Validate;
use think\Request;

/**
 * 客户合作情况
 * @internal
 */
class CustomerCooperSituation extends Backend
{

    protected $noNeedLogin = [];
    protected $noNeedRight = [];
    protected $layout = '';

    public function _initialize()
    {
        parent::_initialize();
        self::predis();
    }
    
    public static function predis()
    {
        //dump(\think\Session::get());exit;
        #Predis::init();//初始化redis集群
    }


    /**
     * 客户合作情况列表
     */
    public function index()
    {
        return $this->view->fetch();

    }


    /**
     * 新增客户合作情况
     */
    public function add()
    {
        return $this->view->fetch();
    }



    /**
     * 编辑客户合作情况
     * @param int $id  合作情况id
     */
    public function update()
    {
        return $this->view->fetch();
    }


    /**
     * 客户合作情况详细
     * @param int $id  合作情况id
     */
    public function detail()
    {
        return $this->view->fetch();
    }


    /**
     * 接口转发接口站点方法
     */
    public function crmAdmin()
    {

    }



}
