<?php

namespace app\admin\controller\group;

use app\common\controller\Backend;
use EasyWeChat\Staff\Session;
use fast\Predis;
use think\Loader;
use think\Validate;
use think\Request;

/**
 * 员工对客户自定义分组
 * @internal
 */
class EmployeeCustomGroup extends Backend
{

    protected $noNeedLogin = ['add','getList'];
    protected $noNeedRight = ['index', 'logout'];
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
     * 员工新增自定义客户分组
     * @param string $name  员工对客户自定义分组名称,
     * @param string $employee_id  员工ID,
     */
    public function add()

    {
       /* //
        $menulist = $this->auth->getSidebar([
            'dashboard'  => 'hot',
            'auth'       => ['new', 'red', 'badge'],
            'auth/admin' => 12,
            'auth/rule'  => 4,
            'general'    => ['18', 'purple'],
        ], $this->view->site['fixedpage']);
        $this->view->assign('menulist', $menulist);
        $this->view->assign('title', __('Home'));
        return $this->view->fetch();*/


        return $this->view->fetch();

    }

    /**
     * 获取员工自定义客户分组列表
     * @param string $employee_id  员工ID,
     */
    public function index()
    {
        /* //
         $menulist = $this->auth->getSidebar([
             'dashboard'  => 'hot',
             'auth'       => ['new', 'red', 'badge'],
             'auth/admin' => 12,
             'auth/rule'  => 4,
             'general'    => ['18', 'purple'],
         ], $this->view->site['fixedpage']);
         $this->view->assign('menulist', $menulist);
         $this->view->assign('title', __('Home'));
         return $this->view->fetch();*/



            return $this->view->fetch();

    }


    /**
     * 验证员工自定义客户分组名称是否唯一
     * @param string $name  员工对客户自定义分组名称,
     */
    public function checkNameUnique()
    {

        if (Request::instance()->isPost()){
            echo 'post';exit;
        }else{echo 'get';exit;
            return $this->view->fetch();
        }
    }


    /**
     * 员工编辑自定义客户分组
     * @param string $id  员工对客户自定义分组ID,
     * @param string $name  员工对客户自定义分组名称,
     * @param string $employee_id  员工ID,
     */
    public function update()

    {
        /* //
         $menulist = $this->auth->getSidebar([
             'dashboard'  => 'hot',
             'auth'       => ['new', 'red', 'badge'],
             'auth/admin' => 12,
             'auth/rule'  => 4,
             'general'    => ['18', 'purple'],
         ], $this->view->site['fixedpage']);
         $this->view->assign('menulist', $menulist);
         $this->view->assign('title', __('Home'));
         return $this->view->fetch();*/


        return $this->view->fetch();

    }


    /**
     * 员工删除自定义客户分组
     * @param string $id  员工对客户自定义分组ID,
     * @param string $employee_id  员工ID,
     */
    public function delete()

    {
        /* //
         $menulist = $this->auth->getSidebar([
             'dashboard'  => 'hot',
             'auth'       => ['new', 'red', 'badge'],
             'auth/admin' => 12,
             'auth/rule'  => 4,
             'general'    => ['18', 'purple'],
         ], $this->view->site['fixedpage']);
         $this->view->assign('menulist', $menulist);
         $this->view->assign('title', __('Home'));
         return $this->view->fetch();*/

        if (Request::instance()->isPost()){
            echo 'post';exit;
        }else{echo 'get';exit;
            return $this->view->fetch();
        }
    }


    /**
     * 员工检查自定义客户分组下有无客户
     * @param string $id  员工对客户自定义分组ID,
     * @param string $employee_id  员工ID,
     */
    public function checkHasCustomer()

    {
        /* //
         $menulist = $this->auth->getSidebar([
             'dashboard'  => 'hot',
             'auth'       => ['new', 'red', 'badge'],
             'auth/admin' => 12,
             'auth/rule'  => 4,
             'general'    => ['18', 'purple'],
         ], $this->view->site['fixedpage']);
         $this->view->assign('menulist', $menulist);
         $this->view->assign('title', __('Home'));
         return $this->view->fetch();*/

        if (Request::instance()->isPost()){
            echo 'post';exit;
        }else{echo 'get';exit;
            return $this->view->fetch();
        }
    }





}
