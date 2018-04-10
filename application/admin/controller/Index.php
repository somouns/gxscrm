<?php

namespace app\admin\controller;


use app\common\controller\Backend;
use EasyWeChat\Staff\Session;
use fast\Predis;
use think\Controller;
use think\Loader;
use think\Validate;


/**
 * 后台首页
 * @internal
 */
class Index extends Backend
{
    protected $noNeedLogin = ['login'];
    protected $noNeedRight = ['index', 'logout'];

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 后台首页
     */
    public function index()
    {
        $this->view->assign('title', __('Home'));
        $this->view->assign('auth',$this->auth->getSidebar());
        return $this->view->fetch();
    }
    /*
     * 管理员登录
     */
    public function login()
    {
        $url = $this->request->get('url', 'index/index');
        if ($this->auth->isLogin()) {
            header('Location: ' . $url);
            exit;
        }
        if ($this->request->isPost()) {
            $username = $this->request->post('loginusername');
            $password = $this->request->post('loginpassword');
            if ($username) {
                $data = [];
                $data['loginusername'] = $this->request->post('loginusername');
                $data['loginpassword'] = $this->request->post('loginpassword');
                $validate = $this->validate($data, 'Admin.ulogin');
            }
            if (true !== $validate) {
                return json(array('code' => '-1', 'message' => $validate));
            }
            
            $result = $this->auth->login($username, $password, 2592000);
            return json($result);
        }
        // 根据客户端的cookie,判断是否可以自动登录
        if ($this->auth->autologin()) {
            $this->redirect($url);
        }
        $this->view->assign('title', __('Login'));
        return $this->view->fetch();
    }

    /**
     * 注销登录
     */
    public function logout()
    {
        $this->auth->logout();

        return json(array('code' => 1, 'message' => '退出成功'));
    }
}
